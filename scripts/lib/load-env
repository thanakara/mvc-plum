#!/usr/bin/env bash

envload() {
    if [[ -f ".env" ]]; then
        set -a  # auto export all variables
        source .env
        set +a
    fi
}

if ! (return 2>/dev/null); then
    envload
fi
