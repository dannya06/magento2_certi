#!/bin/bash
if [ -f /home/mage2user/site/current/pub/media/cloudfront$1.flag ]; then
    sh cfpurge.sh
    mv /home/mage2user/site/current/pub/media/cloudfront$1.flag /home/mage2user/site/current/pub/media/cloudfront$2.flag
fi
