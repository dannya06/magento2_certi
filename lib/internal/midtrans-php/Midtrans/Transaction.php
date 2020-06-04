<?php

namespace Midtrans;

use Magento\Framework\App\Filesystem\DirectoryList;
$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
$filesystem = $object_manager->get('Magento\Framework\Filesystem');
$root = $filesystem->getDirectoryRead(DirectoryList::ROOT);
$lib_file = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/ApiRequestor.php');
$conf_file = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/Config.php');
require_once($lib_file);
require_once($conf_file);

class Transaction {

    public static function status($id)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $req = $om->create('Midtrans\ApiRequestor');
        $conf = $om->create('Midtrans\Config');
        return $req->get(
            $conf->getBaseUrl() . '/' . $id . '/status',
            $conf->getServerKey(),
            false
        );
    }

    public static function approve($id)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $req = $om->create('Midtrans\ApiRequestor');
        $conf = $om->create('Midtrans\Config');
        return $req->post(
            $conf->getBaseUrl() . '/' . $id . '/approve',
            $conf->getServerKey(),
            false
        )->status_code;
    }

    public static function cancel($id)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $req = $om->create('Midtrans\ApiRequestor');
        $conf = $om->create('Midtrans\Config');
        return $req->post(
            $conf->getBaseUrl() . '/' . $id . '/cancel',
            $conf->getServerKey(),
            false
        )->status_code;
    }

    public static function expire($id)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $req = $om->create('Midtrans\ApiRequestor');
        $conf = $om->create('Midtrans\Config');
        return $req->post(
            $conf->getBaseUrl() . '/' . $id . '/expire',
            $conf->getServerKey(),
            false)->status_code;
    }

    public static function refund($id, $params)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $req = $om->create('Midtrans\ApiRequestor');
        $conf = $om->create('Midtrans\Config');
        return $req->post(
            $conf->getBaseUrl() . '/' . $id . '/refund',
            $conf->getServerKey(),
            $params)->status_code;
    }

    public static function refundDirect($id, $params)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $req = $om->create('Midtrans\ApiRequestor');
        $conf = $om->create('Midtrans\Config');
        return $req->post(
            $conf->getBaseUrl() . '/' . $id . '/refund/online/direct',
            $conf->getServerKey(),
            $params
        );
    }

    public static function deny($id)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $req = $om->create('Midtrans\ApiRequestor');
        $conf = $om->create('Midtrans\Config');
        return $req->post(
            $conf->getBaseUrl() . '/' . $id . '/deny',
            $conf->getServerKey(),
            false
        );
    }
}