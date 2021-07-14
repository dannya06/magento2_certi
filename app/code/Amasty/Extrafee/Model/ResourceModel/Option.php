<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Option extends AbstractDb
{
    const TABLE_NAME = 'amasty_extrafee_option';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }
}
