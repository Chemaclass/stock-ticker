#!/bin/bash

set -e

if docker ps | grep -q ticker_news; then
    docker-compose exec -T ticker_news composer test-all
else
    echo "Are you sure Docker is running?"
fi
