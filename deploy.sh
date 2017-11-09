#!/bin/bash
echo "starting deployment"
php bin/magento maintenance:enable
php bin/magento setup:upgrade
echo "remove var" 
rm -rf var/di var/generation/ var/page_cache/ var/report/ var/view_preprocessed var/cache/ var/tmp/ var/generation
echo "starting compile" 
php bin/magento setup:di:compile
echo "Clean up static-content"
php bin/magento weltpixel:cleanup
echo "Generate Weltpixel less"
php bin/magento weltpixel:less:generate
echo "stating deploy static files" 
php bin/magento setup:static-content:deploy
echo "clear cache" 
rm -rf var/di var/generation/
php bin/magento cache:flush
php bin/magento maintenance:disable
chmod -R 777 var/ pub/static/ bin/magento
echo "Deployment done" 
