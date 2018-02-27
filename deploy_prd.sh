# mirasz@icube.us
# magento 2.1/2.2 minimum downtime deployment

domain_name=$(hostname -f)
email="sysadmin@icube.us"

release_version=$(date +%Y%m%d%H%M%S)

site_dir=/home/mage2user/site
release=/releases/
current=/current
shared=/shared
pub_dir=pub
log_dir=var/log
web_dir=$site_dir$releases$release_version
shr_dir=$site_dir$shared

read_repo=""
read_branch=""

COMBI=`getopt -o h --long fix-permission,full,composer-install,help -- "$@"` 
eval set -- "$COMBI"

while true; do
case "$1" in

	# --fix-permission
	--fix-permission) CASE_FIXPERM='Fix file & folder permission'; shift
	echo "$CASE_FIXPERM"

		cd $web_dir
		find $pub_dir/ -type f -print0 | xargs -0 chmod 664
		find $pub_dir/ -type d -print0 | xargs -0 chmod 775
		find $log_dir/ -type f -print0 | xargs -0 chmod 664
		find $log_dir/ -type d -print0 | xargs -0 chmod 775
		;;

	# --full
	--full) CASE_FULL='Full deployment WITHOUT composer install'; shift
	echo "$CASE_FULL"

		# Identify repository & branch
		if [ -z "$read_repo" ]
		then
			read -p "Repository name (example: git@github.com:icubeus/swift.git): " read_repo
		fi
		if [ -z "$read_branch" ]
		then
			read -p "Branch to be deployed (example: master): " read_branch
		fi

		echo "Clone latest code"
		cd $site_dir$releases 
		git clone $read_repo $release_version
		cd $web_dir
		git checkout $read_branch

		cd $web_dir
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		php bin/magento setup:di:compile
		php bin/magento setup:static-content:deploy
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush

		echo "Update Symlink"
		ln -s $shr_dir/media/ $site_dir$current/$pub_dir/media
		ln -s $shr_dir/config/env.php $site_dir$current/app/etc/env.php
		rm $site_dir/current
		ln -s $web_dir $site_dir/current
		;;

	# --composer-install
	--composer-install) CASE_CI='Full deployment WITH composer install'; shift
	echo "$CASE_CI"

		# Identify repository & branch
		if [ -z "$read_repo" ]
		then
			read -p "Repository name (example: git@github.com:icubeus/swift.git): " read_repo
		fi
		if [ -z "$read_branch" ]
		then
			read -p "Branch to be deployed (example: master): " read_branch
		fi

		echo "Clone latest code"
		cd $site_dir$releases 
		git clone $read_repo $release_version
		cd $web_dir
		git checkout $read_branch

		cd $web_dir
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		composer install --no-dev --optimize-autoloader
		php bin/magento setup:di:compile
		php bin/magento setup:static-content:deploy
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush

		echo "Update Symlink"
		ln -s $shr_dir/media/ $site_dir$current/$pub_dir/media
		ln -s $shr_dir/config/env.php $site_dir$current/app/etc/env.php
		rm $site_dir/current
		ln -s $web_dir $site_dir/current
		;;

	# --help
	-h|--help) CASE_H='Help Page'; shift
	echo "$CASE_H"

		printf "\n"
		printf "Example usage:\n\n"
		printf "Deployment with all options enabled WITHOUT composer install:\n"
		printf "  bash deploy_prd.sh --full\n\n"
		printf "Deployment with all options enabled WITH composer install:\n"
		printf "  bash deploy_prd.sh --composer-install\n\n"
		printf "Deployment with fixing file & folder permission in pub/ & var/log/ directory:\n"
		printf "  bash deploy_prd.sh --full --fix-permission\t\t\t OR\n"
		printf "  bash deploy_prd.sh --composer-install --fix-permission\n\n"
		exit 0 ;;

	--) shift; break ;;
	*) echo "Try -h for example usage."; exit 1 ;;

esac
done
