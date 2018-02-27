#!/bin/bash
echo "starting deployment"
php bin/magento maintenance:enable
php bin/magento setup:upgrade
echo "remove var" 
rm -rf var/di var/generation/ var/page_cache/ var/report/ var/view_preprocessed var/cache/ var/tmp/ var/generation
echo "Clean up static-content"
php bin/magento weltpixel:cleanup
echo "Generate Weltpixel less"
php bin/magento weltpixel:less:generate
echo "clear cache" 
php bin/magento cache:flush
php bin/magento maintenance:disable
echo "Deployment done" 
