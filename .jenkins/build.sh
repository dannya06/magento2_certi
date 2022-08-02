#!/bin/bash
if [ -z "$1" ] ; then
        echo 'pipeline are setuped incorrectly'
        exit 1
fi
bid=$(echo $BUILD_ID)
ts=$(date +"%Y%m%d")
githead=$(git rev-list -1 tags/$2)
set -ex
echo "Building Backend Docker image with tag " $$githead-$$ts$$bid
echo $githead-$ts$bid

docker login -u _json_key -p "$(cat /etc/service_key/service-account-key.json | tr '\n' ' ')" https://asia.gcr.io

# Build apps image
docker build -t asia.gcr.io/sirclo-iii-nonprod/$1:$2-$ts$bid -f Dockerfile.swift24 .
docker push asia.gcr.io/sirclo-iii-nonprod/$1:$2-$ts$bid
