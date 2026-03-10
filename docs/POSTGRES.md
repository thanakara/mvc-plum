# Postgres Service in Dagger

## Overview

This document summarizes how the PostgreSQL service is structured, validated, and integrated across the service network in Dagger, along with common gotchas encountered along the way.

---

## Service Definition

```python
PG_LOCAL_STORAGE = DOCKER_DIR.joinpath("storage") / "postgresql"

@function
def postgres_service(
    self,
    source: SourceDir,
    user: str,
    db: str,
    password: dagger.Secret,
) -> dagger.Service:
    """PostgreSQL Database Service."""
    return (
        dag.container()
        .from_("postgres:16-alpine")
        .with_(self.set_env_vars(user, db, password))
        .with_mounted_directory(
            "/var/lib/postgresql/data",
            source.directory(PG_LOCAL_STORAGE.as_posix()),
        )
        .with_exposed_port(5432)
        .as_service()
    )
```

### How the data directory is resolved

Since `SourceDir` uses `DefaultPath("/")`, the entire project is snapshotted at call time — including `docker/storage/postgresql`. The flow is:

```
host filesystem
    → source snapshot (entire project, including docker/storage/postgresql)
        → source.directory("docker/storage/postgresql")
            → mounted at /var/lib/postgresql/data in postgres container
```

---

## Environment Variables Helper

```python
def set_env_vars(self, user: str, db: str, password: dagger.Secret):
    def inner(ctr: dagger.Container) -> dagger.Container:
        return (
            ctr.with_env_variable("POSTGRES_USER", user)
            .with_env_variable("POSTGRES_DB", db)
            .with_secret_variable("POSTGRES_PASSWORD", password)
        )
    return inner
```

Secrets must use `.with_secret_variable()`, never `.with_env_variable()`.

---

## PSQL CLI

```python
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
    )
```

Call with:
```bash
dagger call psql-cli --user=root --db=postgres --password=env:DB_PASSWORD terminal
```

The `terminal` flag is a **CLI flag**, not a Python method — `.terminal()` chained in Python returns a `Container` object and does not open an interactive session from the CLI.

`PG*` env vars mean you just type `psql` in the terminal and connect instantly — no flags needed.

---

## Service Dependency Chain

```
postgres  (independent)
    ↑
php-fpm   (depends on postgres)
    ↑
nginx     (depends on php-fpm)
```

Credentials bubble up through the chain:

```bash
dagger call nginx-service --user=root --db=postgres --password=env:DB_PASSWORD up --ports 8000:80
```

---

## Validation

### Ephemeral credentials pattern

```python
# Class-level defaults for ephemeral services
f_usr = "fake-user"
f_dbn = "fake-dbname"
f_pwd = dag.set_secret("POSTGRES_PASSWORD", plaintext="fake-pwd.123")
```

### validate_postgres_service

```python
@function
@check
async def validate_postgres_service(self, source: SourceDir) -> None:
    async with managed_service(
        self.postgres_service(source=source, user=self.f_usr, db=self.f_dbn, password=self.f_pwd)
    ) as pg:
        await (
            dag.container()
            .from_("postgres:16-alpine")
            .with_service_binding("postgres", pg)
            .with_exec(
                ["pg_isready", "-h", "postgres", "-U", self.f_usr, "-d", self.f_dbn],
                expect=dagger.ReturnType.ANY,
            )
            .sync()
        )
```

### validate_nginx_service — full stack with AsyncExitStack

When running `dagger check validate-*`, all checks run in parallel. Services that depend on others must explicitly manage startup order. Nesting `managed_service` works but creates deep indentation. `AsyncExitStack` is the clean solution:

```python
@function
@check
async def validate_nginx_service(self, source: SourceDir) -> None:
    async with contextlib.AsyncExitStack() as stack:
        pg = await stack.enter_async_context(
            managed_service(
                self.postgres_service(
                    source=source, user=self.f_usr, db=self.f_dbn, password=self.f_pwd
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
            .with_directory("/etc/nginx/conf.d", source.directory(NGINX_CONF_DIR.as_posix()))
            .with_exec(["rm", "/etc/nginx/conf.d/default.conf"])
            .with_service_binding("php82-fpm", php)
            .with_exposed_port(80)
            .as_service(use_entrypoint=True)
            .start()
        )
```

Startup order is guaranteed: **postgres → php-fpm → nginx**, each fully up before the next starts. All services stop cleanly on exit.

---

## Common Gotchas

### 1. `with_mounted_directory` is a one-way snapshot
Dagger's `Directory` type represents a **state snapshot**, not a live bind mount like Docker volumes. Data written inside the container does NOT persist back to the host. Use Docker Compose for persistent local development.

### 2. `dagger.EnvVariable` is not a real type
Use plain `str` for user/db parameters. Only `dagger.Secret` is a real Dagger type for sensitive values.

### 3. Typo in image name
`postgres:16-apline` will silently fail. Always double-check: `postgres:16-alpine`.

### 4. Correct postgres data path
The correct mount target is `/var/lib/postgresql/data` (not `/var/lib/postgres/data`).

### 5. `terminal()` is a CLI flag, not a Python chain
`.terminal()` in Python just returns a `Container`. Use `terminal` at the end of a `dagger call` command instead.

### 6. Parallel checks cause DNS failures
Running `dagger check validate-*` runs all checks in parallel. If php-fpm tries to resolve the postgres hostname at startup and postgres isn't bound yet, you get:
```
lookup <hash> on 10.87.0.1:53: no such host
```
Fix: use `managed_service` with `AsyncExitStack` to enforce startup order in each validator.

### 7. Secrets must be sourced before calling
```bash
source .env
dagger call postgres-service --user=root --db=postgres --password=env:DB_PASSWORD up --ports 5432:5432
```
If `DB_PASSWORD` is not set in the shell, Dagger will error.

### 8. Windows Git Bash TTY issue
Interactive `terminal` sessions fail in Git Bash on Windows:
```
The terminal process "bash.exe '--login', '-i'" terminated with exit code: 1
```
Use PowerShell or prefix with `winpty`:
```bash
winpty dagger call psql-cli --user=root --db=postgres --password=env:DB_PASSWORD terminal
```

---

## Dagger vs Docker Compose

| Concern | Docker Compose | Dagger |
|---|---|---|
| Local dev with persistence | ✅ | ❌ (read-only snapshot) |
| CI / integration checks | ❌ | ✅ |
| Reproducible builds | ❌ | ✅ |
| Live data writes | ✅ | ❌ |
| Service dependency ordering | `depends_on` | `managed_service` + `AsyncExitStack` |

**Rule of thumb:** Use Docker Compose for local development and data persistence. Use Dagger for CI, integration validation, and reproducible builds.
