# mirasz@icube.us
# magento 2.1/2.2 minimum downtime deployment

web_dir=/home/mage2user/site/current/
pub_dir=pub/
log_dir=var/log/

COMBI=`getopt -o h --long fix,fwc,nsg,fix-permission,full,composer-install,no-setup-upgrade,static-deploy-only,help -- "$@"` 
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
		php bin/magento cache:flush
		php bin/magento setup:di:compile
		php bin/magento deploy:mode:set developer
		php bin/magento weltpixel:cleanup
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento deploy:mode:set production -s
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		;;

	# --composer-install
	--composer-install) CASE_CI='Full deployment WITH composer install'; shift
	echo "$CASE_CI"

		cd $web_dir
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		composer install --no-dev --optimize-autoloader
		php bin/magento setup:di:compile
		php bin/magento deploy:mode:set developer
		php bin/magento weltpixel:cleanup
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy
		php bin/magento maintenance:enable
		php bin/magento setup:upgrade --keep-generated
		php bin/magento deploy:mode:set production -s
		php bin/magento maintenance:disable
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		;;

	# --no-setup-upgrade
	--no-setup-upgrade) CASE_NSG='Full deployment WITHOUT setup upgrade'; shift
	echo "$CASE_NSG"

		cd $web_dir
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/
		php bin/magento cache:flush
		php bin/magento setup:di:compile
		php bin/magento deploy:mode:set developer
		php bin/magento weltpixel:cleanup
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy
		php bin/magento deploy:mode:set production -s
		rm -rf var/cache/ var/page_cache/ var/di/ var/generation/ var/tmp/ var/report/
		php bin/magento cache:flush
		;;

	# --static-deploy-only
	--static-deploy-only) CASE_DO='Static content deploy only'; shift
	echo "$CASE_DO"

		cd $web_dir
		rm -rf var/cache/ var/page_cache/
		php bin/magento cache:flush
		php bin/magento deploy:mode:set developer
		php bin/magento weltpixel:cleanup
		php bin/magento weltpixel:less:generate
		php bin/magento setup:static-content:deploy
		php bin/magento deploy:mode:set production -s
		rm -rf var/cache/ var/page_cache/
		php bin/magento cache:flush
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
		printf "Deployment with all options enabled WITHOUT composer install AND setup upgrade:\n"
		printf "  bash deploy_local.sh --no-setup-upgrade\n\n"
		printf "Static content deploy only:\n"
		printf "  bash deploy_local.sh --static-deploy-only\n\n"
		printf "Deployment with fixing file & folder permission in pub/ & var/log/ directory:\n"
		printf "  bash deploy_local.sh --fix-permission --full\t\t OR\n"
		printf "  bash deploy_local.sh --fix-permission --composer-install\t OR\n"
		printf "  bash deploy_local.sh --fix-permission --no-setup-upgrade\t OR\n"
		printf "  bash deploy_local.sh --fix-permission --static-deploy-only\n\n"
		exit 0 ;;

	--) shift; break ;;
	*) echo "Try -h for example usage."; exit 1 ;;

esac
done
