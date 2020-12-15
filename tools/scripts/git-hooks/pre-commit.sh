#!/bin/bash

set -e

if docker ps | grep -q stock_ticker; then
    docker-compose exec -T stock_ticker composer csrun
    docker-compose exec -T stock_ticker composer test-unit
else
    echo "Are you sure Docker is running?"
fi
