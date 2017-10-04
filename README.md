# ICUBE SWIFT
This is SWIFT code base using Magento CE 2.1.8


Instalation:

    git clone https://github.com/icubeus/swift.git
    
    git checkout 1.1.0 (install without sample data)  /  git checkout 1.0.0 (install with sample data)

    composer install --prefer-dist

    install site using wizard from browser

    git checkout 2.0.1

    composer update

    php bin/magento setup:upgrade

    php bin/magento setup:di:compile

    php bin/magento setup:static-content:deploy

    php bin/magento cache:flush


Additional extension that excluded from SWIFT:

1. Rapidflow. Can install using composer https://github.com/icubeus/magento2-rapidflow