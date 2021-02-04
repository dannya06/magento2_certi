<?php

namespace Midtrans;

use Magento\Framework\App\Filesystem\DirectoryList;
$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
$filesystem = $object_manager->get('Magento\Framework\Filesystem');
$root = $filesystem->getDirectoryRead(DirectoryList::ROOT);
$lib_file = $root->getAbsolutePath('lib/internal/midtrans-php/Midtrans/Transaction.php');
require_once($lib_file);

class Notification {

    private $response;

    public function __construct($input_source = "php://input")
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $om->get('Psr\Log\LoggerInterface');
        $trans = $om->create('Midtrans\Transaction');
        $raw_notification = json_decode(file_get_contents($input_source), true);
        $status_response = $trans->status($raw_notification['transaction_id']);
        $this->response = $status_response;
    }

    public function __get($name)
    {
        if (isset($this->response->$name)) {
            return $this->response->$name;
        }
    }

    public function getResponse()
    {
        return $this->response;
    }
}

?>