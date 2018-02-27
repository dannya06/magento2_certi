<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Rma\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /** @var UpgradeSchema120 */
    private $upgradeSchema120;

    /**
     * @param UpgradeSchema120 $upgradeSchema120
     */
    public function __construct(
        UpgradeSchema120 $upgradeSchema120
    ) {
        $this->upgradeSchema120 = $upgradeSchema120;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->removeCustomerIdForeignKey($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->upgradeSchema120->upgrade($setup);
        }

        $setup->endSetup();
    }

    /**
     * Remove customer id foreign key
     *
     * @param SchemaSetupInterface $setup
     */
    private function removeCustomerIdForeignKey(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->dropForeignKey(
            $setup->getTable('aw_rma_request'),
            $setup->getFkName(
                'aw_rma_request',
                'customer_id',
                'customer_entity',
                'entity_id'
            )
        );
    }
}
