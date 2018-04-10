# mirasz@icube.us
# magento 2.2 minimum downtime deployment

web_dir=/home/mage2user/site/current/
pub_dir=pub/
log_dir=var/log/
merged_dir=pub/static/_cache/merged
weltpixel_dir=app/code/WeltPixel

COMBI=`getopt -o h --long fix-permission,full,composer-install,no-setup-upgrade,setup-upgrade,composer-new-module,static-deploy-only,ca-cl,ca-fl,help -- "$@"` 
eval set -- "$COMBI"

while true; do
case "$1" in

	# --fix-permission
	--fix-permission) CASE_FIXPERM='Fix file & folder permission'; shift
	echo "$CASE_FIXPERM"

		cd $web_dir
		find $pub_dir -type f -print0 | xargs -0 chmod 664
		find $pub_dir -type d -print0 | xargs -0 chmod 775
		find $log_dir -type f -print0 | xargs -0 chmod 664
		find $log_dir -type d -print0 | xargs -0 chmod 775
		;;

	# --full
	--full) CASE_FULL='Full deployment WITHOUT composer install'; shift
	echo "$CASE_FULL"

		cd $web_dir
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		rm -rf $merged_dir/*
		php bin/magento cache:flush
		php bin/magento setup:di:compile
		php bin/magento deploy:mode:set developer
		rm -rf generated/*
		rm -rf generated/*
		php bin/magento weltpixel:cleanup
		find $weltpixel_dir/FrontendOptions/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/FrontendOptions/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomFooter/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomFooter/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomHeader/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomHeader/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CategoryPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CategoryPage/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/ProductPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/ProductPage/view -type f -print0 | xargs chmod 664
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy -f
		php bin/magento weltpixel:css:generate --store=default
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento deploy:mode:set production -s
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# --composer-install
	--composer-install) CASE_CI='Full deployment WITH composer install'; shift
	echo "$CASE_CI"

		cd $web_dir
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		composer install
		php bin/magento setup:di:compile
		php bin/magento deploy:mode:set developer
		rm -rf generated/*
		rm -rf generated/*
		php bin/magento weltpixel:cleanup
		find $weltpixel_dir/FrontendOptions/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/FrontendOptions/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomFooter/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomFooter/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomHeader/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomHeader/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CategoryPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CategoryPage/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/ProductPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/ProductPage/view -type f -print0 | xargs chmod 664
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy -f
		php bin/magento weltpixel:css:generate --store=default
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento deploy:mode:set production -s
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# --no-setup-upgrade
	--no-setup-upgrade) CASE_NSU='Full deployment WITHOUT setup upgrade'; shift
	echo "$CASE_NSU"

		cd $web_dir
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		php bin/magento setup:di:compile
		php bin/magento deploy:mode:set developer
		rm -rf generated/*
		rm -rf generated/*
		php bin/magento weltpixel:cleanup
		find $weltpixel_dir/FrontendOptions/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/FrontendOptions/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomFooter/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomFooter/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomHeader/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomHeader/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CategoryPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CategoryPage/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/ProductPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/ProductPage/view -type f -print0 | xargs chmod 664
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy -f
		php bin/magento weltpixel:css:generate --store=default
		php bin/magento deploy:mode:set production -s
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# install new module: magento
	--setup-upgrade) CASE_SU='Magento setup:upgrade only -- install new module'; shift
	echo "$CASE_SU"

		cd $web_dir
		php bin/magento cache:flush
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento maintenance:disable
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# install new module: composer
	--composer-new-module) CASE_CO='Composer install only -- install new module'; shift
	echo "$CASE_CO"

		cd $web_dir
		php bin/magento cache:flush
		composer install
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# --static-deploy-only
	--static-deploy-only) CASE_ST='Magento static content deploy only'; shift
	echo "$CASE_ST"

		cd $web_dir
		rm -rf var/cache/ var/page_cache/
		php bin/magento cache:flush
		php bin/magento deploy:mode:set developer
		php bin/magento weltpixel:cleanup
		find $weltpixel_dir/FrontendOptions/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/FrontendOptions/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomFooter/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomFooter/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CustomHeader/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CustomHeader/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/CategoryPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/CategoryPage/view -type f -print0 | xargs chmod 664
		find $weltpixel_dir/ProductPage/view -type d -print0 | xargs chmod 775
		find $weltpixel_dir/ProductPage/view -type f -print0 | xargs chmod 664
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy -f
		php bin/magento weltpixel:css:generate --store=default
		php bin/magento deploy:mode:set production -s
		rm -rf var/cache/ var/page_cache/
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# --ca-cl
	--ca-cl) CASE_CL='Magento cache:clean'; shift
	echo "$CASE_CL"

		cd $web_dir
		php bin/magento cache:clean
		php bin/magento cache:enable
		;;

	# --ca-fl
	--ca-fl) CASE_FL='Magento cache:flush'; shift
	echo "$CASE_FL"

		cd $web_dir
		php bin/magento cache:flush
		php bin/magento cache:enable
		;;

	# --help
	-h|--help) CASE_H='Help Page'; shift
	echo "$CASE_H"

		printf "\n"
		printf "Example usage:\n\n"
		printf "Deployment with all options enabled WITHOUT composer install:\n"
		printf "  bash deploy_local.sh --full\n\n"
		printf "Deployment with all options enabled WITH composer install:\n"
		printf "  bash deploy_local.sh --composer-install\n\n"
		printf "Deployment with all options enabled WITHOUT composer install AND setup upgrade in current code version:\n"
		printf "  bash deploy_local.sh --no-setup-upgrade\n\n"
		printf "Static content deploy only:\n"
		printf "  bash deploy_local.sh --static-deploy-only\n\n"
		printf "Deployment with fixing file & folder permission in pub/ & var/log/ directory:\n"
		printf "  bash deploy_local.sh --fix-permission --full\t\t OR\n"
		printf "  bash deploy_local.sh --fix-permission --composer-install\t OR\n"
		printf "  bash deploy_local.sh --fix-permission --no-setup-upgrade\t OR\n"
		printf "  bash deploy_local.sh --fix-permission --static-deploy-only\n\n"
		printf "Clean cache ONLY in current code version:\n"
		printf "  bash deploy_local.sh --ca-cl\n\n"
		printf "Flush cache ONLY in current code version:\n"
		printf "  bash deploy_local.sh --ca-fl\n\n"
		exit 0 ;;

	--) shift; break ;;
	*) echo "Try -h for example usage."; exit 1 ;;

esac
done
