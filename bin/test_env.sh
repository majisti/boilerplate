#!/usr/bin/env bash

set -e

docker-compose -p test_$(pwd | xargs basename | tr -cd 'A-Za-z0-9_-') \
    -f docker-compose.yml \
    -f docker-compose.test.yml $*
