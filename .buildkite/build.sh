#!/bin/bash
if [ -z "$1" ] ; then
        echo 'pipeline are setuped incorrectly'
        exit 1
fi

# build docker image
ts=$(date +%s)
githead=$(git rev-list -1 tags/$SWIFT_TAG)
set -ex
echo "Building Backend Docker image with tag " $$githead-$$ts
echo $githead-$ts | buildkite-agent meta-data set "image-tag" 
docker login -u _json_key -p "$(cat /etc/service_key/service-account-key.json | tr '\n' ' ')" https://asia.gcr.io
git checkout tags/$SWIFT_TAG
#docker build --build-arg VCS_REF=$(git rev-list -1 tags/$SWIFT_TAG) -t asia.gcr.io/sirclo-iii-nonprod/$1:$SWIFT_TAG -f Dockerfile.swift24 .
docker build -t asia.gcr.io/sirclo-iii-nonprod/$1:$SWIFT_TAG-$ts -f Dockerfile.swift24 .
docker push asia.gcr.io/sirclo-iii-nonprod/$1:$SWIFT_TAG-$ts