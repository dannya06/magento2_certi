<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Setup;

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Repository
     */
    private $attributeRepository;

    /**
     * @var \Amasty\Base\Setup\SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;

    public function __construct(
        Repository $attributeRepository,
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Amasty\Base\Setup\SerializedFieldDataConverter $fieldDataConverter
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->fieldDataConverter = $fieldDataConverter;
        $this->productMetaData = $productMetaData;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (!$context->getVersion()) {
            return;
        }

        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.2.2', '<')
            && $this->productMetaData->getVersion() >= "2.2.0"
        ) {
            $this->fieldDataConverter->convertSerializedDataToJson(
                'amasty_extrafee',
                'entity_id',
                ['conditions_serialized']
            );
        }

        $setup->endSetup();
    }
}
