<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\ResourceModel;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExtrafeeCreditmemo extends AbstractDb
{
    const TABLE_NAME = 'amasty_extrafee_creditmemo';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ExtrafeeCreditmemoInterface::ENTITY_ID);
    }
}
