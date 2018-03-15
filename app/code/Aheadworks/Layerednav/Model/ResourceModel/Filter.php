<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Filter
 * @package Aheadworks\Layerednav\Model\ResourceModel
 * @codeCoverageIgnore
 */
class Filter extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_layerednav_filter', 'id');
    }
}
