#!/usr/bin/env bash

set -e
source .env
arg=${1:-}

arg-logic() {
    if [[ "$arg" != "up" && "$arg" != "down" ]]; then
        echo "[error]: InvalidArgument. [up/down]" >&2
        exit 1

    elif [[ "$arg" == "up" ]]; then
        cd "docker"
        local app_service="$(basename "$PWD")-$APP_SERVICE-1"
        docker compose --env-file ../.env up -d
        echo "[info]: __network_up__"
        docker exec -ti $app_service bash

    else
        cd "docker"
        docker compose --env-file ../.env down
        echo "[info]: __network_down__"
    fi
}

arg-logic
