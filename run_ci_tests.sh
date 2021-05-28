#!/bin/bash

source variables

docker run --rm -e HOME=/var/www -e GIT_COMMITTER_NAME=docker -e GIT_COMMITTER_EMAIL=docker@docker \
  -u $(id -u $USER) -v $(pwd):/var/www/ -t $DOCKER_WEB_NAME bash -c \
  './composer.phar install --no-ansi --no-interaction && \
    ./composer.phar self-update --no-ansi --no-interaction && \
    ./vendor/bin/phpunit --debug --verbose -d zend.enable_gc=0 --configuration phpunit.xml --exclude-group sqlite3 test'
