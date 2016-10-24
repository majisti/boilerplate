#!/usr/bin/env bash

set -e

directoryName=$(pwd | xargs basename | tr -cd 'A-Za-z0-9_-')

docker-compose -p test_${directoryName} \
    -f docker-compose.yml \
    -f docker-compose.test.yml $*
