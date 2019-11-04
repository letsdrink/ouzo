#!/bin/bash

source variables

ARGS=""
if [ $# -ne 0 ]
then
    ARGS=$@
fi
docker run --rm -e HOME=/var/www -e GIT_COMMITTER_NAME=docker -e GIT_COMMITTER_EMAIL=docker@docker \
    -u `id -u $USER` -v $(pwd):/var/www/ -t $DOCKER_WEB_NAME /var/www/console $ARGS
