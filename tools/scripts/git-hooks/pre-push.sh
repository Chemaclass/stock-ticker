#!/bin/bash

set -e

if docker ps | grep -q fin_yahoo; then
    docker-compose exec -T fin_yahoo composer test-all
else
    echo "Are you sure Docker is running?"
fi
