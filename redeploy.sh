#!/bin/bash
if [ -f /home/mage2user/site/current/pub/media/deploy$1.flag ]; then
	php /home/mage2user/site/current/bin/magento maintenance:enable
	rm -rf /home/mage2user/site/current/var/view_processed/* /home/mage2user/site/current/pub/static/*
	php /home/mage2user/site/current/bin/magento weltpixel:cleanup
	php /home/mage2user/site/current/bin/magento weltpixel:less:generate
	php /home/mage2user/site/current/bin/magento setup:static-content:deploy -f en_US id_ID
	php /home/mage2user/site/current/bin/magento weltpixel:css:generate --store=default
	php /home/mage2user/site/current/bin/magento cache:enable
	sudo service varnish restart
	php /home/mage2user/site/current/bin/magento maintenance:disable
    mv /home/mage2user/site/current/pub/media/deploy$1.flag /home/mage2user/site/current/pub/media/deploy$2.flag
fi
