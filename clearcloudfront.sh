#!/bin/bash
if [ -f pub/media/cloudfront$1.flag ]; then
    sh cfpurge.sh
    mv pub/media/cloudfront$1.flag pub/media/cloudfront$2.flag
fi
