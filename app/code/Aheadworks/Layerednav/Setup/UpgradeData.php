<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Setup;

use Aheadworks\Layerednav\Model\FilterManagement;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 *
 * @package Aheadworks\Layerednav\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var FilterManagement
     */
    private $filterManagement;

    /**
     * @param FilterManagement $filterManagement
     */
    public function __construct(
        FilterManagement $filterManagement
    ) {
        $this->filterManagement = $filterManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->synchronizeFilters();
        }

        $setup->endSetup();
    }

    /**
     * Synchronize filters
     */
    private function synchronizeFilters()
    {
        $this->filterManagement->synchronizeCustomFilters();
        $this->filterManagement->synchronizeAttributeFilters();
    }
}
