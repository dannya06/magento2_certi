<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\ResourceModel;

use Amasty\Extrafee\Api\Data\ExtrafeeInvoiceInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExtrafeeInvoice extends AbstractDb
{
    const TABLE_NAME = 'amasty_extrafee_invoice';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ExtrafeeInvoiceInterface::ENTITY_ID);
    }
}
