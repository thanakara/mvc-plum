#!/usr/bin/env bash

set -e

cli="${1:-app}"

source scripts/lib/compose

fetch-cli() {

    if [[ "$cli" != "app" && "$cli" != "db" ]]; then
        echo "[error]: InvalidArgument. [app/db]" >&2
        exit 1

    elif [[ "$cli" == "app" ]]; then
        docker exec -ti "$APP_SERVICE" bash

    else
        # -e injects PGPASSWORD variable / -ti executes interactively
        docker exec -e PGPASSWORD="$PG_PASSWORD" -ti "$DB_SERVICE" \
        psql -U "$PG_USER" -h "$PG_HOST" "$PG_DB"
    fi
}

cd "docker"
# if no services are up exit; else choose your cli
[[ ! -n $(docker ps -a -q) ]] && \
echo "[warning]: ServicesNotFound" || fetch-cli
