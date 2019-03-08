#!/bin/bash
if [ -f /home/mage2user/site/current/pub/media/varnish$1.flag ]; then
    sudo service varnish restart
    mv /home/mage2user/site/current/pub/media/varnish$1.flag /home/mage2user/site/current/pub/media/varnish$2.flag
fi
