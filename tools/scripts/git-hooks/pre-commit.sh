#!/bin/bash

set -e

if docker ps | grep -q ticker_news; then
    docker-compose exec -T ticker_news composer csrun
    docker-compose exec -T ticker_news composer test-unit
else
    echo "Are you sure Docker is running?"
fi
