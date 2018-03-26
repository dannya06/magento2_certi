# ICUBE SWIFT
This is SWIFT code base using Magento CE 2.1.10


Instalation:
============================================================

    git clone https://github.com/icubeus/swift.git
    
    git checkout 1.1.0 (install without sample data)  /  git checkout 1.0.0 (install with sample data)

    composer install --prefer-dist

    install site using wizard from browser

    git checkout 2.0.10

    composer install

    php bin/magento setup:upgrade

    php bin/magento setup:di:compile

    php bin/magento setup:static-content:deploy

    php bin/magento cache:flush


How to update project that base from SWIFT
=============================================================

    git remote add swift https://github.com/icubeus/swift.git

    git fetch swift --tags

    git merge 2.0.10
    
    composer install
    
    full deploy : setup upgrade, compile, deploy

    DO SOME TESTING

    git push origin <your branch>


How to update project that clone from SWIFT
=============================================================

    git fetch origin --tags

    git checkout tags/2.0.10 -b <branch_name>_2.0.10
    
    composer install

    full deploy : setup upgrade, compile, deploy



Additional extension that excluded from SWIFT:

- Rapidflow. Can install using composer https://github.com/icubeus/magento2-rapidflow


How to get ShareThis Script and put into admin page
=============================================================

To enable share to social media (facabook,twitter,whassap,etc), please follow below step

* Go to https://platform.sharethis.com/login (if don't have a account, please create first)
* After login, in the sidebar menu go to Share Buttons > Inline Share Buttons
* Crete new Property in top left corner (dropdown list) > Setup New Property
* Setup your share button what do you want (Inline Share Button = On)
* After you setup, click "update" button in the bottom, after that go to above to click "Get the code" button
* Copy Paste the script and paste in Magento Admin
* Paste in Content > Design, Configuration > Choose your Theme
* Go to HTML Head > Scripts and Style Sheets. Paste your script and click "Save Configuration" button
* Clear all magento cache


Update CMS Blocks Sample from Weltpixel
=============================================================

We strip out contents of CMS Blocks Sample from Weltpixel that has {{widget}} type of "Magento/CatalogWidget",

from : *swift/app/code/WeltPixel/SampleData/fixtures/blocks/blocks.csv*

because it will create error during setup:upgrade process if the project does not have sample data installed.
Those 5 widgets are  :

* section_content1_v5
* section_content2_v5
* section_content3_v5
* latest_product_v8
* content5_shopnow_v10

Update them all after Swift installation by running SQL script located on :
###### sql/weltpixel_samplecmsblock_update_for_catalogwidget.sql
