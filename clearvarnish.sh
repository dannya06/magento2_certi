#!/bin/bash
if [ -f pub/media/varnish$1.flag ]; then
    sudo service varnish restart
    mv pub/media/varnish$1.flag pub/media/varnish$2.flag
fi
