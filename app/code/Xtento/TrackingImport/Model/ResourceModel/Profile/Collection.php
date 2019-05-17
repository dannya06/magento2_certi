<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            MlbKB4xzfXDFlN04cZrwR1LbEaw8WMlnyA9rcd7bvA8=
 * Last Modified: 2017-08-02T13:07:40+00:00
 * File:          app/code/Xtento/TrackingImport/Model/ResourceModel/Profile/Collection.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\ResourceModel\Profile;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Xtento\TrackingImport\Model\Profile', 'Xtento\TrackingImport\Model\ResourceModel\Profile');
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $configuration = $item->getData('configuration');
            if (!is_array($configuration)) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $utilsHelper = $objectManager->create('\Xtento\XtCore\Helper\Utils');
                if (version_compare($utilsHelper->getMagentoVersion(), '2.2', '>=')) {
                    $item->setData('configuration', json_decode($configuration));
                } else {
                    $item->setData('configuration', unserialize($configuration));
                }
                $item->setDataChanges(false);
            }
        }
        return $this;
    }
}