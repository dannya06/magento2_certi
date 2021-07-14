<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ExtrafeeCreditmemoInterface::ENTITY_ID;

    protected function _construct()
    {
        $this->_init(\Amasty\Extrafee\Model\ExtrafeeCreditmemo::class, ExtrafeeCreditmemo::class);
    }
}
