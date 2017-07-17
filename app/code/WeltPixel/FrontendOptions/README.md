# m2-weltpixel-frontend-options

### Installation

Dependencies:
 - m2-weltpixel-backend

With composer:

```sh
$ composer config repositories.welpixel-m2-weltpixel-frontend-options git git@github.com:rusdragos/m2-weltpixel-frontend-options.git
$ composer require weltpixel/m2-weltpixel-frontend-options:dev-master
```

Manually:

Copy the zip into app/code/WeltPixel/FrontendOptions directory


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_FrontendOptions --clear-static-content
$ php bin/magento setup:upgrade
```
