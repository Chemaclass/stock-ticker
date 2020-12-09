#!/bin/bash

set -e

if docker ps | grep -q fin_yahoo; then
    docker-compose exec -T fin_yahoo composer csrun
    docker-compose exec -T fin_yahoo composer test-unit
else
    echo "Are you sure Docker is running?"
fi
