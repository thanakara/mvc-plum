# Dagger Gotchas — PHP/Nginx Stack

A collection of lessons learned building the PHP-FPM + Nginx stack in Dagger.

---

## 1. Build from Dockerfile instead of replicating it with `with_exec`

Instead of manually chaining `with_exec` calls to replicate your Dockerfile, use `source.docker_build()`. The Dockerfile stays as the single source of truth:

```python
# ❌ fragile and redundant
dag.container().from_("php:8.2-fpm").with_exec(["apt-get", "install", ...])

# ✅ use the actual Dockerfile
source.docker_build(dockerfile="docker/Dockerfile")
```

---

## 2. Always use `use_entrypoint=True` for PHP-FPM and Nginx

Both `php:8.2-fpm` and `nginx:alpine` rely on their own entrypoint scripts for correct startup. Without this flag, Dagger bypasses the entrypoint and the service fails:

```python
ctr.as_service(use_entrypoint=True)
```

---

## 3. `with_directory` merges, it does not replace

Mounting a directory with `with_directory` merges its contents into the target path — it does not wipe what was already there. This means `default.conf` from `nginx:alpine` persists alongside your own config:

```python
# ❌ default.conf is still there after this
ctr.with_directory("/etc/nginx/conf.d", source.directory("docker/nginx"))

# ✅ remove default.conf after the mount
ctr.with_directory("/etc/nginx/conf.d", source.directory("docker/nginx"))
   .with_exec(["rm", "/etc/nginx/conf.d/default.conf"])
```

---

## 4. Order matters — `with_exec` only affects layers after it

`with_exec(["rm", ...])` only removes a file from the layer it runs on. Running it before `with_directory` is pointless because the mount re-adds the file:

```python
# ❌ rm runs before the mount, so default.conf comes back
ctr.with_exec(["rm", "/etc/nginx/conf.d/default.conf"])
   .with_directory("/etc/nginx/conf.d", source.directory("docker/nginx"))

# ✅ rm runs after the mount
ctr.with_directory("/etc/nginx/conf.d", source.directory("docker/nginx"))
   .with_exec(["rm", "/etc/nginx/conf.d/default.conf"])
```

---

## 5. Service bindings are one-directional

Only the service that initiates connections needs the binding. Nginx calls PHP-FPM, so only Nginx needs `.with_service_binding()`. PHP-FPM has no need to know about Nginx:

```python
# Nginx container only
ctr.with_service_binding("php82-fpm", self.php_fpm_service(source=source))
```

---

## 6. The service binding alias must match the upstream name in nginx.conf

The alias passed to `.with_service_binding()` gets injected into `/etc/hosts` inside the container. It must exactly match what your `nginx.conf` uses as the `fastcgi_pass` host:

```python
# in Dagger
ctr.with_service_binding("php82-fpm", self.php_fpm_service(source=source))

# in nginx.conf — must match
fastcgi_pass php82-fpm:9000;
```

---

## 7. Run `composer install` inside the container, not from the host

The `vendor/` directory should never be copied in from the host. It is heavy and should be generated inside the container as part of the build. Keep `vendor` in the `Ignore` list and run Composer in `php_base`:

```python
SourceDir = Annotated[
    dagger.Directory,
    DefaultPath("/"),
    Ignore([".git", "**/__pycache__", "node_modules", "vendor"]),  # vendor excluded
]

# run composer inside the container
source.docker_build(dockerfile="docker/Dockerfile")
      .with_directory("/var/www", source)
      .with_workdir("/var/www")
      .with_exec(["composer", "install"])
      .with_exec(["composer", "dump-autoload"])
```

---

## 8. `.terminal()` blocks service startup

Placing `.terminal()` in the chain opens an interactive shell but the service hasn't started yet — so `up --ports` will show nothing. Use `.terminal()` only for inspection, and comment it out before running the service:

```python
ctr.with_directory("/var/www", source)
   .terminal()          # good for inspecting filesystem
   .as_service(...)     # service starts after you exit the terminal
```

---

## 9. Use a test function to bypass browser and port caching

When `dagger call nginx-service up --ports 8000:80` seems to serve a stale response, bypass it entirely by querying the service from within Dagger's own network:

```python
@function
async def test_nginx(self, source: SourceDir) -> str:
    return await (
        dag.container()
        .from_("alpine")
        .with_exec(["apk", "add", "curl"])
        .with_service_binding("nginx", self.nginx_service(source=source))
        .with_exec(["curl", "-v", "http://nginx:80"])
        .stdout()
    )
```

This avoids browser cache and host port forwarding entirely.

---

## 10. Run `dagger` commands from the project root

`DefaultPath("/")` resolves relative to where you run `dagger` from. Running from inside `.dagger/` will mount the wrong directory as `source`:

```bash
# ❌ wrong — mounts .dagger/ as source
cd .dagger && dagger call nginx-service up --ports 8000:80

# ✅ correct — mounts project root as source
cd project-root && dagger call nginx-service up --ports 8000:80
```