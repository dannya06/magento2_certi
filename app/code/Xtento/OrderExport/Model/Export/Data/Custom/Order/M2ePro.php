<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            MlbKB4xzfXDFlN04cZrwR1LbEaw8WMlnyA9rcd7bvA8=
 * Last Modified: 2018-11-20T10:46:19+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Data/Custom/Order/M2ePro.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Data\Custom\Order;

use Xtento\OrderExport\Model\Export;

class M2ePro extends \Xtento\OrderExport\Model\Export\Data\AbstractData
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * M2ePro constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\XtCore\Helper\Date $dateHelper
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\XtCore\Helper\Date $dateHelper,
        \Xtento\XtCore\Helper\Utils $utilsHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $dateHelper, $utilsHelper, $resource, $resourceCollection, $data);
        $this->objectManager = $objectManager;
    }

    public function getConfiguration()
    {
        return [
            'name' => 'M2ePro eBay/Amazon Data Export',
            'category' => 'Order',
            'description' => 'Export additional data stored by the M2ePro extension',
            'enabled' => true,
            'apply_to' => [Export::ENTITY_ORDER, Export::ENTITY_INVOICE, Export::ENTITY_SHIPMENT, Export::ENTITY_CREDITMEMO],
            'third_party' => true,
            'depends_module' => 'Ess_M2ePro',
        ];
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = [];

        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if ($this->fieldLoadingRequired('m2epro_ebay') || $this->fieldLoadingRequired('m2epro_amazon')) {
            try {
                // Get M2e order_id
                $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
                $readAdapter = $resource->getConnection();
                $table = $resource->getTableName('m2epro_order');
                $binds = [
                    'magento_order_id' => $order->getId(),
                ];
                $dataRow = $readAdapter->fetchRow("SELECT * FROM {$table} WHERE magento_order_id = :magento_order_id", $binds);
                $m2eOrderId = isset($dataRow['id']) ? $dataRow['id'] : false;
            } catch (\Exception $e) {

            }
        }

        if (!empty($m2eOrderId) && $this->fieldLoadingRequired('m2epro_ebay')) {
            try {
                $this->writeArray = &$returnArray['m2epro_ebay'];
                $table = $resource->getTableName('m2epro_ebay_order');
                $binds = [
                    'order_id' => $m2eOrderId,
                ];
                $dataRow = $readAdapter->fetchRow("SELECT * FROM {$table} WHERE order_id = :order_id", $binds);

                foreach ($dataRow as $key => $value) {
                    $this->writeValue($key, $value);
                }
            } catch (\Exception $e) {

            }
            $this->writeArray = &$returnArray;
        }

        if ($this->fieldLoadingRequired('m2epro_amazon')) {
            try {
                $this->writeArray = &$returnArray['m2epro_amazon'];
                $table = $resource->getTableName('m2epro_amazon_order');
                $binds = [
                    'order_id' => $m2eOrderId,
                ];
                $dataRow = $readAdapter->fetchRow("SELECT * FROM {$table} WHERE order_id = :order_id", $binds);

                foreach ($dataRow as $key => $value) {
                    $this->writeValue($key, $value);
                }
            } catch (\Exception $e) {

            }
            $this->writeArray = &$returnArray;
        }

        // Done
        return $returnArray;
    }
}