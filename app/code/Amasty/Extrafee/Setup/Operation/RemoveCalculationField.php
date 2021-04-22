<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup\Operation;

use Amasty\Extrafee\Model\ResourceModel\Fee;
use Magento\Framework\Setup\SchemaSetupInterface;

class RemoveCalculationField
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(Fee::TABLE_NAME);
        $connection = $setup->getConnection();

        $connection->dropColumn($table, 'tax_in_subtotal');
    }
}
