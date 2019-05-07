# magento 2 minimum downtime deployment

site_dir=/home/mage2user/site
releases=/releases
current=/current
release_version=$(date +%Y%m%d%H%M%S)

printf "Deployment is starting\n"
printf "=====================================\n"
printf "Delete old pre code\n"
printf "=====================================\n";

rm -rf $site_dir$releases/pre_code
mkdir $site_dir$releases/pre_code
cp -R $site_dir$current/. $site_dir$releases/pre_code/

printf "=====================================\n"
printf "Fetch pre code from related branch\n"
printf "=====================================\n"

cd $site_dir$releases/pre_code && git fetch origin
cd $site_dir$releases/pre_code && git checkout -b $release_version origin/master

printf "=====================================\n"
printf "Deployment in pre code folder\n"
printf "=====================================\n"

rm -rf $site_dir$releases/pre_code/var/cache $site_dir$releases/pre_code/generated/* s$site_dir$releases/pre_code/var/view_preprocessed/* $site_dir$releases/pre_code/pub/static/*
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
if [ $status -eq 1 ]; then 
	php $site_dir$releases/pre_code/bin/magento setup:upgrade
else
	printf "No need setup upgrade\n"
fi


printf "=====================================\n"
printf "Fetch pre code from related branch\n"
printf "=====================================\n"


cd $site_dir$current && git fetch origin
cd $site_dir$current && git checkout -b $release_version origin/master

printf "=====================================\n"
printf "Copying file from pre code to production\n"
printf "=====================================\n"

cd $site_dir$current && composer install
rm -rf $site_dir$current/var/generated/*
rm -rf $site_dir$current/var/view_preprocessed/*
rm -rf $site_dir$current/pub/static/*
cp -r $site_dir$releases/pre_code/generated/* $site_dir$current/generated/
cp -r $site_dir$releases/pre_code/pub/static/* $site_dir$current/pub/static/

php $site_dir$current/bin/magento setup:db:status
status=$?
if [ $status -eq 1 ]; then 
	php $site_dir$current/bin/magento maintenance:enable
	php $site_dir$current/bin/magento setup:upgrade
	sudo service varnish restart
	php $site_dir$current/bin/magento maintenance:disable
else
	printf "No need setup upgrade\n"
fi


php $site_dir$current/bin/magento cache:flush
php $site_dir$current/bin/magento cache:enable
sudo service varnish restart

printf "====================================="
printf "Deployment is done\n"
printf "=====================================\n"