import contextlib
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
PG_LOCAL_STORAGE = DOCKER_DIR.joinpath("storage") / "postgresql"


# Async.CtxMngr which starts & closes a dagger.Service
@contextlib.asynccontextmanager
async def managed_service(svc: dagger.Service):
    yield await svc.start()
    await svc.stop()


@object_type
class BasicNet:
    # Default credentials used for ephemeral services
    f_usr = "fake-user"
    f_dbn = "fake-dbname"
    f_pwd = dag.set_secret("POSTGRES_PASSWORD", plaintext="fake-pwd.123")

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
    def php_fpm_service(
        self, source: SourceDir, user: str, db: str, password: dagger.Secret
    ) -> dagger.Service:
        """Runs PHP-FPM as a Dagger Service."""
        return (
            self.php_base(source=source)
            .with_service_binding(
                "postgres", self.postgres_service(source, user, db, password)
            )
            .with_exposed_port(9000)
            .as_service(use_entrypoint=True)  # let php-fpm's entrypoint handle startup
        )

    @function
    @check
    async def validate_php_fpm_service(self, source: SourceDir) -> None:
        """Validates that the PHP-FPM service starts successfully."""
        async with managed_service(
            self.postgres_service(
                source=source, user=self.f_usr, db=self.f_dbn, password=self.f_pwd
            )
        ) as pg:
            await (
                self.php_base(source=source)
                .with_service_binding("postgres", pg)
                .with_exposed_port(9000)
                .as_service(use_entrypoint=True)
                .start()
            )

    # __NGINX
    @function
    def nginx_service(
        self, source: SourceDir, user: str, db: str, password: dagger.Secret
    ) -> dagger.Service:
        """Runs NGINX as a Dagger Service."""
        return (
            dag.container()
            .from_("nginx:alpine")
            # .with_env_variable("CACHE_BUST", str(uuid.uuid4()))
            .with_directory("/var/www", source)
            .with_directory(
                "/etc/nginx/conf.d", source.directory(NGINX_CONF_DIR.as_posix())
            )
            .with_exec(["rm", "/etc/nginx/conf.d/default.conf"])  # after the mount
            .with_service_binding(
                "php82-fpm", self.php_fpm_service(source, user, db, password)
            )
            .with_exposed_port(80)
            .as_service(use_entrypoint=True)
        )

    @function
    @check
    async def validate_nginx_service(self, source: SourceDir) -> None:
        """Validates that the NGINX service starts successfully."""

        # enter_async_context() -ing in dependency order:
        async with contextlib.AsyncExitStack() as stack:
            pg = await stack.enter_async_context(
                managed_service(
                    self.postgres_service(
                        source=source,
                        user=self.f_usr,
                        db=self.f_dbn,
                        password=self.f_pwd,
                    )
                )
            )
            php = await stack.enter_async_context(
                managed_service(
                    self.php_base(source=source)
                    .with_service_binding("postgres", pg)
                    .with_exposed_port(9000)
                    .as_service(use_entrypoint=True)
                )
            )
            await (
                dag.container()
                .from_("nginx:alpine")
                .with_directory("/var/www", source)
                .with_directory(
                    "/etc/nginx/conf.d", source.directory(NGINX_CONF_DIR.as_posix())
                )
                .with_exec(["rm", "/etc/nginx/conf.d/default.conf"])
                .with_service_binding("php82-fpm", php)
                .with_exposed_port(80)
                .as_service(use_entrypoint=True)
                .start()
            )

    # __POSTGRES
    def set_env_vars(self, user: str, db: str, password: dagger.Secret):
        """Helper method to properly set ENV vars in a Container."""

        def inner(ctr: dagger.Container) -> dagger.Container:
            return (
                ctr.with_env_variable("POSTGRES_USER", user)
                .with_env_variable("POSTGRES_DB", db)
                .with_secret_variable("POSTGRES_PASSWORD", password)
            )

        return inner

    @function
    def postgres_service(
        self,
        source: SourceDir,
        user: str,
        db: str,
        password: dagger.Secret,
    ) -> dagger.Service:
        """PostgreSQL Database Service."""
        # pg_data = dag.cache_volume("pg-data")

        return (
            dag.container()
            .from_("postgres:16-alpine")
            .with_(self.set_env_vars(user, db, password))
            # .with_mounted_cache("/var/lib/postgresql/data", pg_data)
            .with_mounted_directory(
                "/var/lib/postgresql/data",
                source.directory(PG_LOCAL_STORAGE.as_posix()),
            )
            .with_exposed_port(5432)
            .as_service()
        )

    @function
    async def psql_cli(
        self,
        source: SourceDir,
        user: str,
        db: str,
        password: dagger.Secret,
    ) -> dagger.Container:
        """Get the psql CLI inside the postgres container."""
        pg_svc = await self.postgres_service(source, user, db, password).start()
        return (
            dag.container()
            .from_("postgres:16-alpine")
            .with_service_binding("postgres", pg_svc)
            .with_env_variable("PGUSER", user)
            .with_env_variable("PGDATABASE", db)
            .with_env_variable("PGHOST", "postgres")
            .with_env_variable("PGPORT", "5432")
            .with_secret_variable("PGPASSWORD", password)
            # .terminal()
        )

    @function
    @check
    async def validate_postgres_service(self, source: SourceDir) -> None:
        """Spinsup ephemeral postgres with defaults to verify connection."""
        async with managed_service(
            self.postgres_service(
                source=source, user=self.f_usr, db=self.f_dbn, password=self.f_pwd
            )
        ) as pg:
            await (
                dag.container()
                .from_("postgres:16-alpine")
                .with_service_binding("postgres", pg)
                .with_exec(
                    [
                        "pg_isready",
                        "-h",
                        "postgres",
                        "-U",
                        self.f_usr,
                        "-d",
                        self.f_dbn,
                    ],
                    expect=dagger.ReturnType.ANY,  # don't throw on non-zero
                )
                .sync()
            )
