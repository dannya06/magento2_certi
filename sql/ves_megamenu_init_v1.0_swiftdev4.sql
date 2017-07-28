# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.18-15)
# Database: magento2
# Generation Time: 2017-07-28 02:26:23 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table ves_megamenu_cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ves_megamenu_cache`;

CREATE TABLE `ves_megamenu_cache` (
  `cache_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT 'Cache ID',
  `menu_id` smallint(6) NOT NULL COMMENT 'Menu ID',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID',
  `html` mediumtext COMMENT 'Menu Html',
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Menu Creation Time',
  PRIMARY KEY (`cache_id`),
  KEY `VES_MEGAMENU_CACHE_MENU_ID` (`menu_id`),
  KEY `VES_MEGAMENU_CACHE_STORE_ID` (`store_id`),
  CONSTRAINT `VES_MEGAMENU_CACHE_MENU_ID_VES_MEGAMENU_MENU_MENU_ID` FOREIGN KEY (`menu_id`) REFERENCES `ves_megamenu_menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `VES_MEGAMENU_CACHE_STORE_ID_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menu Log';

LOCK TABLES `ves_megamenu_cache` WRITE;
/*!40000 ALTER TABLE `ves_megamenu_cache` DISABLE KEYS */;

INSERT INTO `ves_megamenu_cache` (`cache_id`, `menu_id`, `store_id`, `html`, `creation_time`)
VALUES
	(2,1,1,'<li id=vesitem-11501208473275470663 class=\" nav-item level0 nav-0 submenu-alignleft subhover  dropdown level-top \"   data-color=\"#000000\" data-hover-color=\"#ffffff\" data-bgcolor=\"transparent\" data-hover-bgcolor=\"#000000\"><a href=\"http://swiftdev4.magedev.shop/women.html\" title=\"Women\" target=\"_self\"  data-hover-color=\"#ffffff\" data-hover-bgcolor=\"#000000\" data-color=\"#000000\" data-bgcolor=\"transparent\" style=\"color: #000000;background-color: transparent;\" class=\" nav-anchor\"><span>Women</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\"  ><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level1 nav-dropdown\"><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-0 mega-col-level-1 col-xs-12\"><div id=1501208473579222539 class=\" nav-item level1 nav-0 submenu-alignleft subhover  dropdown-submenu  parent\"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women.html\" title=\"Tops\" target=\"_self\"   class=\" nav-anchor\"><span>Tops</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\" data-width=\"100%\" style=\"width:100%;\"><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level2 nav-dropdown ves-column1\"><div class=\"item-content1 hidden-xs hidden-sm\"><div class=\"mega-col mega-col-4 mega-col-level-2 col-xs-12\"><div id=1501208473137032992 class=\" nav-item level2 nav-0 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/jackets-women.html\" title=\"Jackets\" target=\"_self\"   class=\" nav-anchor\"><span>Jackets</span></a></div><div id=1501208473445677483 class=\" nav-item level2 nav-1 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/hoodies-and-sweatshirts-women.html\" title=\"Hoodies & Sweatshirts\" target=\"_self\"   class=\" nav-anchor\"><span>Hoodies & Sweatshirts</span></a></div><div id=15012084731749081590 class=\" nav-item level2 nav-2 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/tees-women.html\" title=\"Tees\" target=\"_self\"   class=\" nav-anchor\"><span>Tees</span></a></div><div id=15012084731508827428 class=\" nav-item level2 nav-3 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/tanks-women.html\" title=\"Bras & Tanks\" target=\"_self\"   class=\" nav-anchor\"><span>Bras & Tanks</span></a></div></div></div><div class=\"item-content2 hidden-lg hidden-md\"><div id=1501208473297381019 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/jackets-women.html\" title=\"Jackets\" target=\"_self\"   class=\" nav-anchor\"><span>Jackets</span></a></div><div id=1501208473450523891 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/hoodies-and-sweatshirts-women.html\" title=\"Hoodies & Sweatshirts\" target=\"_self\"   class=\" nav-anchor\"><span>Hoodies & Sweatshirts</span></a></div><div id=15012084731598421615 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/tees-women.html\" title=\"Tees\" target=\"_self\"   class=\" nav-anchor\"><span>Tees</span></a></div><div id=150120847352927569 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/tops-women/tanks-women.html\" title=\"Bras & Tanks\" target=\"_self\"   class=\" nav-anchor\"><span>Bras & Tanks</span></a></div></div></div></div></div></div></div></div></div></div><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-1 mega-col-level-1 col-xs-12\"><div id=15012084731567893326 class=\" nav-item level1 nav-1 submenu-alignleft subhover  dropdown-submenu  parent\"  ><a href=\"http://swiftdev4.magedev.shop/women/bottoms-women.html\" title=\"Bottoms\" target=\"_self\"   class=\" nav-anchor\"><span>Bottoms</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\" data-width=\"100%\" style=\"width:100%;\"><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level2 nav-dropdown ves-column1\"><div class=\"item-content1 hidden-xs hidden-sm\"><div class=\"mega-col mega-col-2 mega-col-level-2 col-xs-12\"><div id=1501208473482183630 class=\" nav-item level2 nav-0 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/bottoms-women/pants-women.html\" title=\"Pants\" target=\"_self\"   class=\" nav-anchor\"><span>Pants</span></a></div><div id=15012084731421487660 class=\" nav-item level2 nav-1 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/bottoms-women/shorts-women.html\" title=\"Shorts\" target=\"_self\"   class=\" nav-anchor\"><span>Shorts</span></a></div></div></div><div class=\"item-content2 hidden-lg hidden-md\"><div id=15012084731490957166 class=\" nav-item level2 nav-2 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/bottoms-women/pants-women.html\" title=\"Pants\" target=\"_self\"   class=\" nav-anchor\"><span>Pants</span></a></div><div id=1501208473528496452 class=\" nav-item level2 nav-2 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/women/bottoms-women/shorts-women.html\" title=\"Shorts\" target=\"_self\"   class=\" nav-anchor\"><span>Shorts</span></a></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></li><li id=vesitem-21501208473722020468 class=\" nav-item level0 nav-0 submenu-alignleft subhover  dropdown level-top \"   data-color=\"#000000\" data-hover-color=\"#ffffff !important\" data-bgcolor=\"transparent\" data-hover-bgcolor=\"#000000\"><a href=\"http://swiftdev4.magedev.shop/men.html\" title=\"Men\" target=\"_self\"  data-hover-color=\"#ffffff !important\" data-hover-bgcolor=\"#000000\" data-color=\"#000000\" data-bgcolor=\"transparent\" style=\"color: #000000;background-color: transparent;\" class=\" nav-anchor\"><span>Men</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\"  ><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level1 nav-dropdown\"><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-0 mega-col-level-1 col-xs-12\"><div id=15012084731248287923 class=\" nav-item level1 nav-0 submenu-alignleft subhover  dropdown-submenu  parent\"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men.html\" title=\"Tops\" target=\"_self\"   class=\" nav-anchor\"><span>Tops</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\" data-width=\"100%\" style=\"width:100%;\"><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level2 nav-dropdown ves-column1\"><div class=\"item-content1 hidden-xs hidden-sm\"><div class=\"mega-col mega-col-4 mega-col-level-2 col-xs-12\"><div id=15012084731202553393 class=\" nav-item level2 nav-0 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/jackets-men.html\" title=\"Jackets\" target=\"_self\"   class=\" nav-anchor\"><span>Jackets</span></a></div><div id=1501208473315760090 class=\" nav-item level2 nav-1 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/hoodies-and-sweatshirts-men.html\" title=\"Hoodies & Sweatshirts\" target=\"_self\"   class=\" nav-anchor\"><span>Hoodies & Sweatshirts</span></a></div><div id=15012084732136582369 class=\" nav-item level2 nav-2 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/tees-men.html\" title=\"Tees\" target=\"_self\"   class=\" nav-anchor\"><span>Tees</span></a></div><div id=15012084731015592717 class=\" nav-item level2 nav-3 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/tanks-men.html\" title=\"Tanks\" target=\"_self\"   class=\" nav-anchor\"><span>Tanks</span></a></div></div></div><div class=\"item-content2 hidden-lg hidden-md\"><div id=15012084731227900401 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/jackets-men.html\" title=\"Jackets\" target=\"_self\"   class=\" nav-anchor\"><span>Jackets</span></a></div><div id=1501208473183694178 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/hoodies-and-sweatshirts-men.html\" title=\"Hoodies & Sweatshirts\" target=\"_self\"   class=\" nav-anchor\"><span>Hoodies & Sweatshirts</span></a></div><div id=1501208473444603548 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/tees-men.html\" title=\"Tees\" target=\"_self\"   class=\" nav-anchor\"><span>Tees</span></a></div><div id=15012084731503371065 class=\" nav-item level2 nav-4 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/tops-men/tanks-men.html\" title=\"Tanks\" target=\"_self\"   class=\" nav-anchor\"><span>Tanks</span></a></div></div></div></div></div></div></div></div></div></div><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-1 mega-col-level-1 col-xs-12\"><div id=1501208473905714647 class=\" nav-item level1 nav-1 submenu-alignleft subhover  dropdown-submenu  parent\"  ><a href=\"http://swiftdev4.magedev.shop/men/bottoms-men.html\" title=\"Bottoms\" target=\"_self\"   class=\" nav-anchor\"><span>Bottoms</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\" data-width=\"100%\" style=\"width:100%;\"><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level2 nav-dropdown ves-column1\"><div class=\"item-content1 hidden-xs hidden-sm\"><div class=\"mega-col mega-col-2 mega-col-level-2 col-xs-12\"><div id=15012084732030601764 class=\" nav-item level2 nav-0 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/bottoms-men/pants-men.html\" title=\"Pants\" target=\"_self\"   class=\" nav-anchor\"><span>Pants</span></a></div><div id=1501208473236660412 class=\" nav-item level2 nav-1 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/bottoms-men/shorts-men.html\" title=\"Shorts\" target=\"_self\"   class=\" nav-anchor\"><span>Shorts</span></a></div></div></div><div class=\"item-content2 hidden-lg hidden-md\"><div id=15012084731372955873 class=\" nav-item level2 nav-2 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/bottoms-men/pants-men.html\" title=\"Pants\" target=\"_self\"   class=\" nav-anchor\"><span>Pants</span></a></div><div id=15012084731316876872 class=\" nav-item level2 nav-2 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/men/bottoms-men/shorts-men.html\" title=\"Shorts\" target=\"_self\"   class=\" nav-anchor\"><span>Shorts</span></a></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></li><li id=vesitem-315012084731585998215 class=\" nav-item level0 nav-0 submenu-alignleft subhover  dropdown level-top \"   data-color=\"#000000\" data-hover-color=\"#ffffff\" data-bgcolor=\"transparent\" data-hover-bgcolor=\"#000000\"><a href=\"http://swiftdev4.magedev.shop/gear.html\" title=\"Gear\" target=\"_self\"  data-hover-color=\"#ffffff\" data-hover-bgcolor=\"#000000\" data-color=\"#000000\" data-bgcolor=\"transparent\" style=\"color: #000000;background-color: transparent;\" class=\" nav-anchor\"><span>Gear</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\"  ><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level1 nav-dropdown\"><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-0 mega-col-level-1 col-xs-12\"><div id=15012084731698575964 class=\" nav-item level1 nav-0 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/gear/bags.html\" title=\"Bags\" target=\"_self\"   class=\" nav-anchor\"><span>Bags</span></a></div></div></div><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-1 mega-col-level-1 col-xs-12\"><div id=1501208473194201781 class=\" nav-item level1 nav-1 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/gear/fitness-equipment.html\" title=\"Fitness Equipment\" target=\"_self\"   class=\" nav-anchor\"><span>Fitness Equipment</span></a></div></div></div><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-2 mega-col-level-1 col-xs-12\"><div id=1501208473276751965 class=\" nav-item level1 nav-2 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/gear/watches.html\" title=\"Watches\" target=\"_self\"   class=\" nav-anchor\"><span>Watches</span></a></div></div></div></div></div></div></div></div></li><li id=vesitem-41501208473880772995 class=\" nav-item level0 nav-0 submenu-alignleft subhover  dropdown level-top \"   data-color=\"#000000\" data-hover-color=\"#ffffff\" data-bgcolor=\"transparent\" data-hover-bgcolor=\"#000000\"><a href=\"http://swiftdev4.magedev.shop/training.html\" title=\"Training\" target=\"_self\"  data-hover-color=\"#ffffff\" data-hover-bgcolor=\"#000000\" data-color=\"#000000\" data-bgcolor=\"transparent\" style=\"color: #000000;background-color: transparent;\" class=\" nav-anchor\"><span>Training</span><span class=\"opener\"></span><span class=\"drill-opener\"></span></a><div class=\" submenu dropdown-menu\"  ><div class=\"drilldown-back\"><a href=\"#\"><span class=\"drill-opener\"></span><span class=\"current-cat\"></span></a></div><div class=\"submenu-inner\"><div class=\"content-wrapper\"><div class=\"item-content\" style=\"width:100%\"><div class=\"level1 nav-dropdown\"><div class=\"row\"><div class=\"mega-col col-sm-12 mega-col-0 mega-col-level-1 col-xs-12\"><div id=1501208473130314855 class=\" nav-item level1 nav-0 submenu-alignleft subhover  dropdown-submenu \"  ><a href=\"http://swiftdev4.magedev.shop/training/training-video.html\" title=\"Video Download\" target=\"_self\"   class=\" nav-anchor\"><span>Video Download</span></a></div></div></div></div></div></div></div></div></li><li id=vesitem-51501208473467241226 class=\" nav-item level0 nav-0 submenu-alignleft subhover  dropdown level-top \"   data-color=\"#000000\" data-hover-color=\"#ffffff\" data-bgcolor=\"transparent\" data-hover-bgcolor=\"#000000\"><a href=\"http://swiftdev4.magedev.shop/sale.html\" title=\"SaleHot!\" target=\"_self\"  data-hover-color=\"#ffffff\" data-hover-bgcolor=\"#000000\" data-color=\"#000000\" data-bgcolor=\"transparent\" style=\"color: #000000;background-color: transparent;\" class=\" nav-anchor\"><span>Sale<span class=\"cat-label cat-label-v2 pin-top\">Hot!</span></span></a></li>','2017-07-28 02:21:13');

/*!40000 ALTER TABLE `ves_megamenu_cache` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ves_megamenu_item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ves_megamenu_item`;

CREATE TABLE `ves_megamenu_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_id` text NOT NULL COMMENT 'ID',
  `name` varchar(255) DEFAULT NULL COMMENT 'Item Name',
  `show_name` smallint(6) DEFAULT NULL COMMENT 'Show Name',
  `classes` varchar(255) DEFAULT NULL COMMENT 'Classes',
  `child_col` varchar(255) DEFAULT NULL COMMENT 'Child Menu Col',
  `sub_width` varchar(255) DEFAULT NULL COMMENT 'Sub Width',
  `align` varchar(255) DEFAULT NULL COMMENT 'Alignment Type',
  `icon_position` varchar(255) DEFAULT NULL COMMENT 'Icon Position',
  `icon_classes` varchar(255) DEFAULT NULL COMMENT 'Icon Classes',
  `is_group` smallint(6) DEFAULT NULL COMMENT 'Is Group',
  `status` smallint(6) DEFAULT NULL COMMENT 'Status',
  `disable_bellow` smallint(6) NOT NULL COMMENT 'Disable Bellow',
  `show_icon` smallint(6) NOT NULL COMMENT 'Show Icon',
  `icon` varchar(255) DEFAULT NULL COMMENT 'Icon',
  `show_header` smallint(6) DEFAULT NULL COMMENT 'Show Header',
  `header_html` text COMMENT 'Header',
  `show_left_sidebar` smallint(6) DEFAULT NULL COMMENT 'Show Left Sidebar',
  `left_sidebar_width` varchar(255) DEFAULT NULL COMMENT 'Left Sidebar Width',
  `menu_id` smallint(6) DEFAULT NULL COMMENT 'Menu ID',
  `left_sidebar_html` text COMMENT 'Left Sidebar HTML',
  `show_content` smallint(6) DEFAULT NULL COMMENT 'Show Content',
  `content_width` varchar(255) DEFAULT NULL COMMENT 'Content Width',
  `content_type` varchar(255) DEFAULT NULL COMMENT 'Content Type',
  `link_type` varchar(255) DEFAULT NULL COMMENT 'Link',
  `link` varchar(255) DEFAULT NULL COMMENT 'Link',
  `category` text COMMENT 'Link',
  `target` varchar(255) DEFAULT NULL COMMENT 'Link',
  `content_html` text COMMENT 'Content HTML',
  `show_right_sidebar` smallint(6) DEFAULT NULL COMMENT 'Show Right Sidebar',
  `right_sidebar_width` varchar(255) DEFAULT NULL COMMENT 'Right Sidebar Width',
  `right_sidebar_html` text COMMENT 'Right Sidebar HTML',
  `show_footer` smallint(6) DEFAULT NULL COMMENT 'Show Footer',
  `footer_html` text COMMENT 'Footer HTML',
  `color` varchar(255) DEFAULT NULL COMMENT 'Color',
  `hover_color` varchar(255) DEFAULT NULL COMMENT 'Hover Color',
  `bg_color` varchar(255) DEFAULT NULL COMMENT 'Background Color',
  `bg_hover_color` varchar(255) DEFAULT NULL COMMENT 'Background Hover Color',
  `inline_css` text COMMENT 'Inline CSS',
  `tab_position` varchar(255) DEFAULT NULL COMMENT 'Tab Position',
  `before_html` mediumtext COMMENT 'Before Html',
  `after_html` mediumtext COMMENT 'After Html',
  `caret` varchar(255) DEFAULT NULL COMMENT 'Caret',
  `hover_caret` varchar(255) DEFAULT NULL COMMENT 'Hover Caret',
  `sub_height` varchar(255) DEFAULT NULL COMMENT 'Sub Height',
  `hover_icon` varchar(255) DEFAULT NULL COMMENT 'Hover Icon',
  `dropdown_bgcolor` varchar(255) DEFAULT NULL COMMENT 'Dropdown Background Color',
  `dropdown_bgimage` varchar(255) DEFAULT NULL COMMENT 'Dropdown Bakground Image',
  `dropdown_bgimagerepeat` varchar(255) DEFAULT NULL COMMENT 'Dropdown Bakground Image Repeat',
  `dropdown_bgpositionx` varchar(255) DEFAULT NULL COMMENT 'Dropdown Background Position X',
  `dropdown_bgpositiony` varchar(255) DEFAULT NULL COMMENT 'Dropdown Background Position Y',
  `dropdown_inlinecss` varchar(255) DEFAULT NULL COMMENT 'Dropdown Inline CSS',
  `parentcat` varchar(255) DEFAULT NULL COMMENT 'Parent Category',
  `animation_in` varchar(255) DEFAULT NULL COMMENT 'Animation In',
  `animation_time` varchar(255) DEFAULT NULL COMMENT 'Animation Time',
  PRIMARY KEY (`id`),
  KEY `VES_MEGAMENU_ITEM_MENU_ID` (`menu_id`),
  CONSTRAINT `VES_MEGAMENU_ITEM_FK_MENU_ID_VES_MEGAMENU_MENU_MENU_ID` FOREIGN KEY (`menu_id`) REFERENCES `ves_megamenu_menu` (`menu_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Megamenu Menu Item';

LOCK TABLES `ves_megamenu_item` WRITE;
/*!40000 ALTER TABLE `ves_megamenu_item` DISABLE KEYS */;

INSERT INTO `ves_megamenu_item` (`id`, `item_id`, `name`, `show_name`, `classes`, `child_col`, `sub_width`, `align`, `icon_position`, `icon_classes`, `is_group`, `status`, `disable_bellow`, `show_icon`, `icon`, `show_header`, `header_html`, `show_left_sidebar`, `left_sidebar_width`, `menu_id`, `left_sidebar_html`, `show_content`, `content_width`, `content_type`, `link_type`, `link`, `category`, `target`, `content_html`, `show_right_sidebar`, `right_sidebar_width`, `right_sidebar_html`, `show_footer`, `footer_html`, `color`, `hover_color`, `bg_color`, `bg_hover_color`, `inline_css`, `tab_position`, `before_html`, `after_html`, `caret`, `hover_caret`, `sub_height`, `hover_icon`, `dropdown_bgcolor`, `dropdown_bgimage`, `dropdown_bgimagerepeat`, `dropdown_bgpositionx`, `dropdown_bgpositiony`, `dropdown_inlinecss`, `parentcat`, `animation_in`, `animation_time`)
VALUES
	(1,'_1501205260260_260','Women',NULL,NULL,'1',NULL,'3','left',NULL,0,1,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','parentcat','category_link',NULL,'20','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'20',NULL,'0.5'),
	(2,'_1501205552276_276','Men',NULL,NULL,'1',NULL,'3','left',NULL,0,1,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','parentcat','category_link',NULL,'11','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff !important','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'11',NULL,'0.5'),
	(3,'_1501205640452_452','Gear',NULL,NULL,'1',NULL,'3','left',NULL,0,1,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','parentcat','category_link',NULL,'3','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'3',NULL,'0.5'),
	(4,'_1501205673437_437','Training',NULL,NULL,'1',NULL,'3','left',NULL,0,1,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','parentcat','category_link',NULL,'9','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'9',NULL,'0.5'),
	(5,'_1501205718201_201','Sale<span class=\"cat-label cat-label-v2 pin-top\">Hot!</span>',NULL,NULL,'1',NULL,'3','left',NULL,0,1,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','parentcat','category_link',NULL,'37','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'37',NULL,'0.5'),
	(6,'_1501205882437_437','Custom Menu',NULL,NULL,'1',NULL,'3','left',NULL,0,0,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','childmenu','custom_link','javascript:void(0)','2','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'2',NULL,'0.5'),
	(9,'_1501206458210_210','Collections',NULL,NULL,'1',NULL,'3','left',NULL,0,0,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','parentcat','category_link',NULL,'7','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'7',NULL,'0.5'),
	(10,'_1501206523832_832','Promotions',NULL,NULL,'1',NULL,'3','left',NULL,0,0,0,0,NULL,0,NULL,0,NULL,1,NULL,1,'100%','parentcat','category_link',NULL,'29','_self',NULL,0,NULL,NULL,0,NULL,'#000000','#ffffff','transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'no-repeat',NULL,NULL,NULL,'29',NULL,'0.5'),
	(11,'_1501206851476_476',NULL,NULL,NULL,'1',NULL,'3','left',NULL,0,0,768,1,'ves/megamenu/home.png',0,NULL,0,NULL,1,NULL,1,'100%','childmenu','custom_link','{{store url=\"\"}}','2','_self',NULL,0,NULL,NULL,0,NULL,NULL,NULL,'transparent','#000000',NULL,'left',NULL,NULL,NULL,NULL,NULL,'ves/megamenu/home_hover.png',NULL,NULL,'no-repeat',NULL,NULL,NULL,'2',NULL,'0.5');

/*!40000 ALTER TABLE `ves_megamenu_item` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ves_megamenu_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ves_megamenu_menu`;

CREATE TABLE `ves_megamenu_menu` (
  `menu_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT 'Menu ID',
  `alias` varchar(255) NOT NULL COMMENT 'Alias',
  `name` varchar(255) NOT NULL COMMENT 'Menu Name',
  `mobile_template` varchar(255) NOT NULL COMMENT 'Mobile Template',
  `structure` text NOT NULL COMMENT 'Structure',
  `disable_bellow` smallint(6) NOT NULL COMMENT 'Disable Bellow',
  `status` smallint(6) NOT NULL COMMENT 'Status',
  `html` text NOT NULL COMMENT 'Html',
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Menu Creation Time',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Menu Modification Time',
  `desktop_template` varchar(255) DEFAULT NULL COMMENT 'Desktop Template',
  `design` mediumtext COMMENT 'Design',
  `params` mediumtext COMMENT 'Params',
  `disable_iblocks` smallint(6) DEFAULT NULL COMMENT 'Disable Item Blocks',
  `event` varchar(255) DEFAULT NULL COMMENT 'Event',
  `classes` varchar(255) DEFAULT NULL COMMENT 'Classes',
  `width` varchar(255) DEFAULT NULL COMMENT 'Width',
  `scrolltofixed` smallint(6) DEFAULT NULL COMMENT 'Scroll to fixed',
  `current_version` varchar(255) DEFAULT NULL COMMENT 'Current Version',
  `mobile_menu_alias` varchar(255) DEFAULT NULL COMMENT 'Mobile menu alias',
  PRIMARY KEY (`menu_id`),
  KEY `VES_MEGAMENU_MENU_MENU_ID` (`menu_id`),
  KEY `VES_MEGAMENU_MENU_ALIAS` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ves_megamenu_menu';

LOCK TABLES `ves_megamenu_menu` WRITE;
/*!40000 ALTER TABLE `ves_megamenu_menu` DISABLE KEYS */;

INSERT INTO `ves_megamenu_menu` (`menu_id`, `alias`, `name`, `mobile_template`, `structure`, `disable_bellow`, `status`, `html`, `creation_time`, `update_time`, `desktop_template`, `design`, `params`, `disable_iblocks`, `event`, `classes`, `width`, `scrolltofixed`, `current_version`, `mobile_menu_alias`)
VALUES
	(1,'top-menu','Megamenu (Top Menu)','1','[{\"id\":\"_1501206851476_476\",\"children\":[]},{\"id\":\"_1501205260260_260\",\"children\":[]},{\"id\":\"_1501205552276_276\",\"children\":[]},{\"id\":\"_1501205640452_452\",\"children\":[]},{\"id\":\"_1501205673437_437\",\"children\":[]},{\"id\":\"_1501206458210_210\",\"children\":[]},{\"id\":\"_1501206523832_832\",\"children\":[]},{\"id\":\"_1501205718201_201\",\"children\":[]},{\"id\":\"_1501205882437_437\",\"children\":[]}]',0,1,'','2017-07-28 01:24:51','2017-07-28 02:19:54','horizontal','a:37:{s:10:\"margin_top\";s:0:\"\";s:12:\"margin_right\";s:0:\"\";s:13:\"margin_bottom\";s:0:\"\";s:11:\"margin_left\";s:0:\"\";s:12:\"margin_units\";s:2:\"px\";s:16:\"border_top_width\";s:0:\"\";s:18:\"border_right_width\";s:0:\"\";s:19:\"border_bottom_width\";s:0:\"\";s:17:\"border_left_width\";s:0:\"\";s:12:\"border_units\";s:2:\"px\";s:11:\"padding_top\";s:0:\"\";s:13:\"padding_right\";s:0:\"\";s:14:\"padding_bottom\";s:0:\"\";s:12:\"padding_left\";s:0:\"\";s:13:\"padding_units\";s:2:\"px\";s:5:\"width\";s:0:\"\";s:12:\"border_color\";s:0:\"\";s:12:\"border_style\";s:0:\"\";s:10:\"background\";s:0:\"\";s:15:\"boxshadow_units\";s:2:\"px\";s:15:\"boxshadow_inset\";s:1:\"0\";s:11:\"boxshadow_x\";s:1:\"0\";s:11:\"boxshadow_y\";s:1:\"0\";s:14:\"boxshadow_blur\";s:1:\"0\";s:16:\"boxshadow_spread\";s:1:\"0\";s:15:\"boxshadow_color\";s:0:\"\";s:9:\"font_size\";s:0:\"\";s:10:\"font_group\";s:6:\"custom\";s:11:\"font_custom\";s:0:\"\";s:11:\"font_google\";s:0:\"\";s:16:\"font_char_subset\";s:8:\"cyrillic\";s:11:\"font_weight\";s:0:\"\";s:22:\"border_top_left_radius\";s:0:\"\";s:23:\"border_top_right_radius\";s:0:\"\";s:26:\"border_bottom_right_radius\";s:0:\"\";s:25:\"border_bottom_left_radius\";s:0:\"\";s:12:\"radius_units\";s:2:\"px\";}','a:2:{s:9:\"structure\";s:379:\"[{\"id\":\"_1501206851476_476\",\"children\":[]},{\"id\":\"_1501205260260_260\",\"children\":[]},{\"id\":\"_1501205552276_276\",\"children\":[]},{\"id\":\"_1501205640452_452\",\"children\":[]},{\"id\":\"_1501205673437_437\",\"children\":[]},{\"id\":\"_1501206458210_210\",\"children\":[]},{\"id\":\"_1501206523832_832\",\"children\":[]},{\"id\":\"_1501205718201_201\",\"children\":[]},{\"id\":\"_1501205882437_437\",\"children\":[]}]\";s:6:\"design\";a:37:{s:10:\"margin_top\";s:0:\"\";s:12:\"margin_right\";s:0:\"\";s:13:\"margin_bottom\";s:0:\"\";s:11:\"margin_left\";s:0:\"\";s:12:\"margin_units\";s:2:\"px\";s:16:\"border_top_width\";s:0:\"\";s:18:\"border_right_width\";s:0:\"\";s:19:\"border_bottom_width\";s:0:\"\";s:17:\"border_left_width\";s:0:\"\";s:12:\"border_units\";s:2:\"px\";s:11:\"padding_top\";s:0:\"\";s:13:\"padding_right\";s:0:\"\";s:14:\"padding_bottom\";s:0:\"\";s:12:\"padding_left\";s:0:\"\";s:13:\"padding_units\";s:2:\"px\";s:5:\"width\";s:0:\"\";s:12:\"border_color\";s:0:\"\";s:12:\"border_style\";s:0:\"\";s:10:\"background\";s:0:\"\";s:15:\"boxshadow_units\";s:2:\"px\";s:15:\"boxshadow_inset\";s:1:\"0\";s:11:\"boxshadow_x\";s:1:\"0\";s:11:\"boxshadow_y\";s:1:\"0\";s:14:\"boxshadow_blur\";s:1:\"0\";s:16:\"boxshadow_spread\";s:1:\"0\";s:15:\"boxshadow_color\";s:0:\"\";s:9:\"font_size\";s:0:\"\";s:10:\"font_group\";s:6:\"custom\";s:11:\"font_custom\";s:0:\"\";s:11:\"font_google\";s:0:\"\";s:16:\"font_char_subset\";s:8:\"cyrillic\";s:11:\"font_weight\";s:0:\"\";s:22:\"border_top_left_radius\";s:0:\"\";s:23:\"border_top_right_radius\";s:0:\"\";s:26:\"border_bottom_right_radius\";s:0:\"\";s:25:\"border_bottom_left_radius\";s:0:\"\";s:12:\"radius_units\";s:2:\"px\";}}',0,'hover',NULL,NULL,0,'13',NULL);

/*!40000 ALTER TABLE `ves_megamenu_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ves_megamenu_menu_customergroup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ves_megamenu_menu_customergroup`;

CREATE TABLE `ves_megamenu_menu_customergroup` (
  `menu_id` smallint(6) NOT NULL COMMENT 'Menu ID',
  `customer_group_id` smallint(5) unsigned NOT NULL COMMENT 'Customer Group ID',
  PRIMARY KEY (`menu_id`,`customer_group_id`),
  KEY `VES_MEGAMENU_MENU_CUSTOMERGROUP_CUSTOMER_GROUP_ID` (`customer_group_id`),
  CONSTRAINT `FK_F6D35C8B9D5FA8FC4EAFFDDBF4793D7E` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_group` (`customer_group_id`) ON DELETE CASCADE,
  CONSTRAINT `VES_MEGAMENU_MENU_CSTRGROUP_MENU_ID_VES_MEGAMENU_MENU_MENU_ID` FOREIGN KEY (`menu_id`) REFERENCES `ves_megamenu_menu` (`menu_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menu Custom Group';

LOCK TABLES `ves_megamenu_menu_customergroup` WRITE;
/*!40000 ALTER TABLE `ves_megamenu_menu_customergroup` DISABLE KEYS */;

INSERT INTO `ves_megamenu_menu_customergroup` (`menu_id`, `customer_group_id`)
VALUES
	(1,0),
	(1,1),
	(1,2),
	(1,3);

/*!40000 ALTER TABLE `ves_megamenu_menu_customergroup` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ves_megamenu_menu_store
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ves_megamenu_menu_store`;

CREATE TABLE `ves_megamenu_menu_store` (
  `menu_id` smallint(6) NOT NULL COMMENT 'Menu ID',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID',
  PRIMARY KEY (`menu_id`,`store_id`),
  KEY `VES_MEGAMENU_MENU_STORE_STORE_ID` (`store_id`),
  KEY `VES_MEGAMENU_MENU_STORE_MENU_ID` (`menu_id`),
  CONSTRAINT `VES_MEGAMENU_MENU_STORE_MENU_ID_VES_MEGAMENU_MENU_MENU_ID` FOREIGN KEY (`menu_id`) REFERENCES `ves_megamenu_menu` (`menu_id`) ON DELETE CASCADE,
  CONSTRAINT `VES_MEGAMENU_MENU_STORE_STORE_ID_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Megamenu Menu Store';

LOCK TABLES `ves_megamenu_menu_store` WRITE;
/*!40000 ALTER TABLE `ves_megamenu_menu_store` DISABLE KEYS */;

INSERT INTO `ves_megamenu_menu_store` (`menu_id`, `store_id`)
VALUES
	(1,0);

/*!40000 ALTER TABLE `ves_megamenu_menu_store` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
