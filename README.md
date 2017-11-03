# ICUBE SWIFT
This is SWIFT code base using Magento CE 2.1.8


Instalation:
============================================================

    git clone https://github.com/icubeus/swift.git
    
    git checkout 1.1.0 (install without sample data)  /  git checkout 1.0.0 (install with sample data)

    composer install --prefer-dist

    install site using wizard from browser

    git checkout 2.0.5

    composer install

    php bin/magento setup:upgrade

    php bin/magento setup:di:compile

    php bin/magento setup:static-content:deploy

    php bin/magento cache:flush


How to update project that base from SWIFT
=============================================================

    git remote add swift https://github.com/icubeus/swift.git

    git fetch swift --tags

    git merge 2.0.5

    full deploy : setup upgrade, compile, deploy

    DO SOME TESTING

    git push origin <your branch>


How to update project that clone from SWIFT
=============================================================

    git fetch origin --tags

    git checkout tags/2.0.5-b <branch_name>_2.0.5

    full deploy : setup upgrade, compile, deploy



Additional extension that excluded from SWIFT:

- Rapidflow. Can install using composer https://github.com/icubeus/magento2-rapidflow