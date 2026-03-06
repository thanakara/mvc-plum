from pathlib import Path
from typing import Annotated

import dagger
from dagger import DefaultPath, Doc, Ignore, check, dag, function, object_type

SourceDir = Annotated[
    dagger.Directory,
    DefaultPath("/"),
    Doc("project source directory"),
    Ignore([".git", "**/__pycache__", "node_modules", "vendor"]),
]

DOCKER_DIR = Path("docker")
NGINX_CONF_DIR = DOCKER_DIR.joinpath("nginx")


@object_type
class BasicNet:
    # __PHP:8.2-FPM
    @function
    def php_base(self, source: SourceDir) -> dagger.Container:
        """
        Builds the PHP 8.2-FPM image from the project Dockerfile.
        Equivalent to: docker build -f docker/Dockerfile .
        """
        return (
            source.docker_build(dockerfile=DOCKER_DIR.joinpath("Dockerfile").as_posix())
            .with_directory("/var/www", source)
            .with_workdir("/var/www")
            .with_exec(["composer", "install"])
            .with_exec(["composer", "dump-autoload"])
        )

    @function
    def php_fpm_service(self, source: SourceDir) -> dagger.Service:
        """
        Runs PHP-FPM as a Dagger Service.

        ```bash
        dagger call php-fpm-service up --ports 9000:9000
        ```
        """
        return (
            self.php_base(source=source)
            .with_exposed_port(9000)
            # .terminal()
            .as_service(use_entrypoint=True)  # let php-fpm's entrypoint handle startup
        )

    @function
    @check
    async def validate_php_fpm_service(self, source: SourceDir) -> None:
        """Validates that the PHP-FPM service starts successfully."""
        await self.php_fpm_service(source=source).start()

    # __NGINX
    @function
    def nginx_service(self, source: SourceDir) -> dagger.Service:
        """
        Runs NGINX as a Dagger Service.

        ```bash
        dagger call nginx-service up --ports 8000:80
        ```
        """
        return (
            dag.container()
            .from_("nginx:alpine")
            # .with_env_variable("CACHE_BUST", str(uuid.uuid4()))
            .with_directory("/var/www", source)
            .with_directory(
                "/etc/nginx/conf.d", source.directory(NGINX_CONF_DIR.as_posix())
            )
            .with_exec(["rm", "/etc/nginx/conf.d/default.conf"])  # after the mount
            .with_service_binding("php82-fpm", self.php_fpm_service(source=source))
            .with_exposed_port(80)
            .as_service(use_entrypoint=True)
        )

    @function
    @check
    async def validate_nginx_service(self, source: SourceDir) -> None:
        """Validates that the NGINX service starts successfully."""
        await self.nginx_service(source=source).start()
