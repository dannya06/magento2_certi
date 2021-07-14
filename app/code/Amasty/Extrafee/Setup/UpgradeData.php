<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Setup;

use Amasty\Base\Setup\SerializedFieldDataConverter;
use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\ResourceModel\Fee;
use Amasty\Extrafee\Setup\Operation\AddToExistFeesEligibleForRefund;
use Amasty\Extrafee\Setup\Operation\QuoteToOrderDataMigration;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Repository
     */
    private $attributeRepository;

    /**
     * @var SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetaData;

    /**
     * @var QuoteToOrderDataMigration
     */
    private $quoteToOrderMigration;

    /**
     * @var AddToExistFeesEligibleForRefund
     */
    private $addToExistFeesEligibleForRefund;

    public function __construct(
        Repository $attributeRepository,
        ProductMetadataInterface $productMetaData,
        SerializedFieldDataConverter $fieldDataConverter,
        QuoteToOrderDataMigration $quoteToOrderMigration,
        AddToExistFeesEligibleForRefund $addToExistFeesEligibleForRefund
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->fieldDataConverter = $fieldDataConverter;
        $this->productMetaData = $productMetaData;
        $this->quoteToOrderMigration = $quoteToOrderMigration;
        $this->addToExistFeesEligibleForRefund = $addToExistFeesEligibleForRefund;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
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
                Fee::TABLE_NAME,
                FeeInterface::ENTITY_ID,
                [FeeInterface::CONDITIONS_SERIALIZED]
            );
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->quoteToOrderMigration->execute($setup);
            $this->addToExistFeesEligibleForRefund->execute($setup);
        }

        $setup->endSetup();
    }
}
