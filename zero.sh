#!/bin/bash
# magento 2 minimum downtime deployment

site_dir=/home/mage2user/site
releases=/releases
current=/current
release_version=$(date +%Y%m%d%H%M%S)

printf "Deployment is starting\n"
printf "=====================================\n"

printf "=====================================\n"
printf "Deployment in pre code folder\n"
printf "=====================================\n"

rm -rf $site_dir$releases/pre_code/var/cache $site_dir$releases/pre_code/generated s$site_dir$releases/pre_code/var/view_preprocessed/* $site_dir$releases/pre_code/pub/static
mkdir $site_dir$releases/pre_code/generated && mkdir $site_dir$releases/pre_code/pub/static
chmod -R 777 $site_dir$releases/pre_code/generated
chmod -R 777 $site_dir$releases/pre_code/pub/static
cd $site_dir$releases/pre_code && composer install
php $site_dir$releases/pre_code/bin/magento setup:di:compile
php $site_dir$releases/pre_code/bin/magento cache:flush
php $site_dir$releases/pre_code/bin/magento weltpixel:less:generate
php $site_dir$releases/pre_code/bin/magento setup:static-content:deploy -f en_US id_ID

printf "=====================================\n"
printf "Setup Upgrade\n"
printf "=====================================\n"

php $site_dir$releases/pre_code/bin/magento setup:db:status
status=$?
if [ $status -ne 0 ]; then 
	php $site_dir$releases/pre_code/bin/magento setup:upgrade
else
	printf "No need setup upgrade\n"
fi


printf "=====================================\n"
printf "Fetch production code from related branch\n"
printf "=====================================\n"


cd $site_dir$current && git fetch origin

printf "=====================================\n"
printf "get latest code from " $1 " branch\n"
printf "=====================================\n"
cd $site_dir$current && git checkout -b $release_version origin/$1

printf "=====================================\n"
printf "create symlink \n"
printf "=====================================\n"

php $site_dir$current/bin/magento maintenance:enable
rm -rf $site_dir$releases/rel_code
mv $site_dir$releases/cur_code $site_dir$releases/rel_code
mv $site_dir$releases/pre_code $site_dir$releases/cur_code
rm -rf $site_dir$current/var/view_preprocessed	
rm -rf $site_dir$current/generated && ln -s $site_dir$releases/cur_code/generated $site_dir$current/generated
rm -rf $site_dir$current/pub/static && ln -s $site_dir$releases/cur_code/pub/static $site_dir$current/pub/static
cd $site_dir$current && composer install
php $site_dir$current/bin/magento maintenance:disable

php $site_dir$current/bin/magento cache:flush
php $site_dir$current/bin/magento cache:enable
sudo service varnish restart

php $site_dir$current/bin/magento setup:db:status
status=$?
if [ $status -ne 0 ]; then 
	php $site_dir$current/bin/magento maintenance:enable
	php $site_dir$current/bin/magento setup:upgrade --keep-generated
	sudo service varnish restart
	php $site_dir$current/bin/magento maintenance:disable
else
	printf "No need setup upgrade\n"
fi


printf "=====================================\n"
printf "Deployment is done\n"
printf "=====================================\n"
