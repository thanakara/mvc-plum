#!/usr/bin/env bash

set -e

# arg="${1:?[error]: ArgumentRequired. [up/down]}"
arg=${1:-}

source scripts/lib/load-env

envload

DB_SERVICE="${DOCKER_DB_SERVICE}"
APP_SERVICE="${DOCKER_APP_SERVICE}"
PG_DB="${DB_NAME}"
PG_HOST="${DB_HOST}"
PG_USER="${DB_USER}"
PG_PASSWORD="${DB_PASSWORD}"


arg-logic() {
    if [[ "$arg" != "up" && "$arg" != "down" ]]; then
        echo "[error]: InvalidArgument. [up/down]" >&2
        exit 1

    elif [[ "$arg" == "up" ]]; then
        cd "docker"
        docker compose --env-file ../.env up -d
        # proper health check loop:
        until docker exec $DB_SERVICE pg_isready -U $PG_USER -h $PG_HOST >/dev/null 2>&1; do
            echo "[debug]: __waiting_for_db__" >&2
            sleep 1
        done
        echo "[info]: __network_up__"

    else
        cd "docker"
        docker compose --env-file ../.env down
        echo "[info]: __network_down__"
    fi
}

if ! (return 2>/dev/null); then
    arg-logic
fi
