## How to installation

1. Setup module via FTP and run magento 2 commands:

The extension include 2 module: Ves_All, Ves_Setup and Ves_Megamenu

- Connect your server with FTP client (example: FileZilla).
- Upload module files in the module packages in to folder: app/code/Ves/Megamenu , app/code/Ves/Megamenu
- Access SSH terminal, then run commands:

```
php bin/magento setup:upgrade --keep-generated
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
```

- To config the module please. Go to admin > Store > Configuration > Venustheme - Extensions > Megamenu
- To manage request quotes. Go to admin > Venustheme > Megamenu PRO > Manage Menus

2. How to create first default menu to show on frontend:
- Create menu profile. Go to admin > Venustheme > Megamenu PRO > Add New Menu
- Create Menu with alias = "top-menu" 
- Flush cache of the site

3. How to change default menu block on frontend:
- Create the menu profile with alias which you want. Example: my-main-menu
- Edit the file "app/code/Ves/Megamenu/view/frontend/layout/default.xml", then change the code:
```
<referenceBlock name="store.menu">
			<block class="Ves\Megamenu\Block\Menu" name="catalog.topnav" template="Ves_Megamenu::topmenu.phtml">
				<arguments>
					<argument name="alias" xsi:type="string">top-menu</argument>
				</arguments> 
			</block>
		</referenceBlock>
```
change the value "top-menu" to your menu profile's alias value, example:
```
<referenceBlock name="store.menu">
			<block class="Ves\Megamenu\Block\Menu" name="catalog.topnav" template="Ves_Megamenu::topmenu.phtml">
				<arguments>
					<argument name="alias" xsi:type="string">my-main-menu</argument>
				</arguments> 
			</block>
		</referenceBlock>
```
- Flush cache of the site.

Also, you can load more menu profile on frontend by use widget type "Ves Megamenu: Menu"

4. How to import module sample profiles:
- Go to admin > Venustheme > Setup > Import
- Import the sample json file which was included in the release module package.