#!/bin/bash

source variables

docker ps |grep "$DOCKER_DB_NAME"
run=$?

docker ps -a|grep "$DOCKER_DB_NAME"
exists=$?
if [ ${exists} -ne 0 ]
then
    echo "###Base does not exist###"
else
    echo "###Base does exist###"
    docker stop $DOCKER_DB_NAME
    docker rm -f $DOCKER_DB_NAME
fi

docker build --pull --rm -f Dockerfile-postgres -t $DOCKER_DB_NAME .
docker run --name $DOCKER_DB_NAME -e POSTGRES_PASSWORD=$DB_PASSWORD -e POSTGRES_USER=$DB_USER -e POSTGRES_DB=$DB_NAME -d $DOCKER_DB_NAME
until docker exec $DOCKER_DB_NAME /usr/lib/postgresql/10/bin/pg_isready; do sleep 2; done

docker rm -f $DOCKER_WEB_NAME
set -e
docker build --pull --rm -f Dockerfile-dev -t $DOCKER_WEB_NAME .
docker run --name $DOCKER_WEB_NAME --link $DOCKER_DB_NAME:$DOCKER_DB_NAME -d -v $(pwd):/var/www/ -v /dev/log:/dev/log -p 8009:80 $DOCKER_WEB_NAME
./run_composer.sh

DOCKER_DB_IP=$(docker inspect $DOCKER_DB_NAME|jq .[].NetworkSettings.IPAddress | tr -d '"')
DOCKER_WEB_IP=$(docker inspect $DOCKER_WEB_NAME|jq .[].NetworkSettings.IPAddress | tr -d '"')

echo
echo "Application available on http://localhost:8009"
echo "  web IP = $DOCKER_WEB_IP"
echo "  database IP = $DOCKER_DB_IP"
echo
echo export PGHOST=$DOCKER_DB_IP
echo export PGUSER=$DB_USER
echo export PGDATABASE=$DB_NAME
echo export PGPASSWORD=$DB_PASSWORD
echo