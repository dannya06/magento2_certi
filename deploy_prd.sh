# mirasz@icube.us
# magento 2.1/2.2 minimum downtime deployment

domain_name=$(hostname -f)
email="sysadmin@icube.us"

release_version=$(date +%Y%m%d%H%M%S)

site_dir=/home/mage2user/site
releases=/releases/
current=/current
shared=/shared
pub_dir=pub
log_dir=var/log
web_dir=$site_dir$releases$release_version
shr_dir=$site_dir$shared

read_repo=""
read_branch=""

COMBI=`getopt -o h --long fix-permission,full,composer-install,setup-upgrade,composer-new-module,static-deploy,ca-cl,ca-fl,help -- "$@"` 
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
		echo "Update Symlink: configuration"
		ln -s $shr_dir/config/env.php $site_dir/releases/$release_version/app/etc/env.php
		ln -s $shr_dir/config/config.php $site_dir/releases/$release_version/app/etc/config.php

		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		php bin/magento setup:di:compile
		php bin/magento setup:static-content:deploy -f
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento deploy:mode:set production -s
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		php bin/magento cache:enable

		echo "Update Symlink: media"
		mv $site_dir/releases/$release_version/$pub_dir/media $site_dir/releases/$release_version/$pub_dir/media.original
		ln -s $shr_dir/media/ $site_dir/releases/$release_version/$pub_dir/media

		echo "Update Symlink: current"
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
		echo "Update Symlink: configuration"
		ln -s $shr_dir/config/env.php $site_dir/releases/$release_version/app/etc/env.php
		ln -s $shr_dir/config/config.php $site_dir/releases/$release_version/app/etc/config.php

		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		composer install
		php bin/magento setup:di:compile
		php bin/magento setup:static-content:deploy -f
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento deploy:mode:set production -s
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		php bin/magento cache:enable

		echo "Update Symlink: media"
		mv $site_dir/releases/$release_version/$pub_dir/media $site_dir/releases/$release_version/$pub_dir/media.original
		ln -s $shr_dir/media/ $site_dir/releases/$release_version/$pub_dir/media

		echo "Update Symlink: current"
		rm $site_dir/current
		ln -s $web_dir $site_dir/current
		;;

	# install new module: magento
	--setup-upgrade) CASE_SU='Magento setup:upgrade -- install new module'; shift
	echo "$CASE_SU"

		cd $site_dir$current
		php bin/magento cache:flush
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento maintenance:disable
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# install new module: composer
	--composer-new-module) CASE_CO='Composer install -- install new module'; shift
	echo "$CASE_CO"

		cd $site_dir$current
		php bin/magento cache:flush
		composer install
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# --static-deploy
	--static-deploy) CASE_ST='Magento static-content:deploy'; shift
	echo "$CASE_ST"

		cd $site_dir$current
		php bin/magento cache:flush
		php bin/magento setup:static-content:deploy -f
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# --ca-cl
	--ca-cl) CASE_CL='Magento cache:clean'; shift
	echo "$CASE_CL"

		cd $site_dir$current
		php bin/magento cache:clean
		php bin/magento cache:enable
		;;

	# --ca-fl
	--ca-fl) CASE_FL='Magento cache:flush'; shift
	echo "$CASE_FL"

		cd $site_dir$current
		php bin/magento cache:flush
		php bin/magento cache:enable
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
		printf "Install new module (magento) ONLY in current code version (no git clone, no deployment):\n"
		printf "  bash deploy_prd.sh --setup-upgrade\n\n"
		printf "Install new module (composer) ONLY in current code version (no git clone, no deployment):\n"
		printf "  bash deploy_prd.sh --composer-new-module\n\n"
		printf "Static content deploy ONLY in current code version (no git clone, no deployment):\n"
		printf "  bash deploy_prd.sh --static-deploy\n\n"
		printf "Clean cache ONLY in current code version (no git clone, no deployment):\n"
		printf "  bash deploy_prd.sh --ca-cl\n\n"
		printf "Flush cache ONLY in current code version (no git clone, no deployment):\n"
		printf "  bash deploy_prd.sh --ca-fl\n\n"
		exit 0 ;;

	--) shift; break ;;
	*) echo "Try -h for example usage."; exit 1 ;;

esac
done
