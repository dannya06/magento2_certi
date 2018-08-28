# ICUBE SWIFT
This is SWIFT code base using Magento CE 2.1.10


Instalation:
============================================================

    git clone https://github.com/icubeus/swift.git
    
    git checkout 1.1.0 (install without sample data)  /  git checkout 1.0.0 (install with sample data)

    composer install --prefer-dist

    install site using wizard from browser

    git checkout master

    composer install

    php bin/magento setup:upgrade

    php bin/magento setup:di:compile

    php bin/magento setup:static-content:deploy

    php bin/magento cache:flush


How to update project that base from SWIFT
=============================================================

    git remote add swift https://github.com/icubeus/swift.git

    git fetch swift --tags

    git merge master
    
    composer install
    
    full deploy : setup upgrade, compile, deploy

    DO SOME TESTING

    git push origin <your branch>


How to update project that clone from SWIFT
=============================================================

    git fetch origin

    git checkout master_swift -b swift_repo/master
    
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

Deployment Script
=============================================================

## For complete usable command and description:

- `bash deploy_local.sh -h`
- `bash deploy_prd.sh -h`

### deploy_local.sh
- for development environment (testingnow.me) usage WITHOUT cloning the latest branch
- deployment ALWAYS done in current directory

### deploy_prd.sh
- for production environment usage (can be used in testingnow.me environment, too)
- deployment ALWAYS do `git clone` in new releases folder
- please provide github **Repository name** (use "Clone with SSH" format) to bypass password prompt
- please provide repository **Branch to be deployed** name

example: 
```
mage2user@testingnow.me:~site/current$ bash deploy_prd.sh --full

Full deployment WITHOUT composer install

Repository name (example: git@github.com:icubeus/swift.git): git@github.com:icubeus/swift.git

Branch to be deployed (example: master): master
```

### overriding password and branch name prompt
- open deploy_prd.sh
- go to line 20 and add your repository name here, example:
    - `read_repo="git@github.com:icubeus/swift.git"`
- go to line 21 and add your repository branch name here, example:
    - `read_branch="master"`

Extension Incompatibility
=============================================================
Up until Weltpixel v.1.6.1, WeltPixel_SearchAutoComplete has incompatibility code with SmileElasticSuite.
It will throw error code of 500 if both of the modules are active when we are performing AJAX Search Request on Header Search Form.
So, the only possible workaround for now is disable the WeltPixel_SearchAutoComplete via admin : (https://cl.ly/r1Ya)
- Admin Panel : Weltpixel > Ajax Search Autocomplete - Ajax Search Settings
- General Settings for Ajax Search Autocomplete : Enable Ajax Search, set the value to NO
- Perform clean cache from Cache Management or do "php bin/magento cache:clean" from CLI
