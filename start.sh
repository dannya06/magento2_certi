# Entrypoint for docker
# For security reason run as user app
su -c "php /home/app/site/bin/magento cache:enable && php /home/app/site/bin/magento cache:flush" - app

# Logging and supervisord
ln -sf /dev/stdout /var/log/nginx/access.log
ln -sf /dev/stderr /var/log/nginx/error.log
ln -sf /dev/stdout /var/log/php-fpm.log
supervisord -c /etc/supervisor/supervisord.conf