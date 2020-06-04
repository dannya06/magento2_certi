<?php
/** 
 * Check PHP version.
 */
if (version_compare(PHP_VERSION, '5.4', '<')) {
    throw new Exception('PHP version >= 5.4 required');
}

// Check PHP Curl & json decode capabilities.
if (!function_exists('curl_init') || !function_exists('curl_exec')) {
    throw new Exception('Midtrans needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Midtrans needs the JSON PHP extension.');
}

use Magento\Framework\App\Filesystem\DirectoryList;
$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
$filesystem = $object_manager->get('Magento\Framework\Filesystem');
$root = $filesystem->getDirectoryRead(DirectoryList::ROOT);
$conf = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/Config.php');
$trans = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/Transaction.php');
$req = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/ApiRequestor.php');
$snapApiReq = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/SnapApiRequestor.php');
$notif = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/Notification.php');
$coreApi = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/CoreApi.php');
$vts = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/Snap.php');
$san = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/Sanitizer.php');

// Configurations
require_once($conf);

// Midtrans API Resources
require_once($trans);

// Plumbing
require_once($req);
require_once($snapApiReq);
require_once($notif);
require_once($coreApi);
require_once($vts);

// Sanitization
require_once($san);