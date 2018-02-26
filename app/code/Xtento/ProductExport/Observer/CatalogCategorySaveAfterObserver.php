<?php

/**
 * Product:       Xtento_ProductExport (2.5.0)
 * ID:            cb9PRAWlxmJOwg/jsj5X3dDv0+dPZORkauC/n26ZNAU=
 * Packaged:      2018-02-26T09:11:38+00:00
 * Last Modified: 2016-04-17T13:03:00+00:00
 * File:          app/code/Xtento/ProductExport/Observer/CatalogCategorySaveAfterObserver.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Observer;

use Xtento\ProductExport\Model\Export;

class CatalogCategorySaveAfterObserver extends AbstractEventObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->handleEvent($observer, self::EVENT_CATALOG_CATEGORY_SAVE_AFTER, Export::ENTITY_CATEGORY);
    }
}
