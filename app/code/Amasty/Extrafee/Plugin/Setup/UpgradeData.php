<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Plugin\Setup;

use Amasty\Base\Setup\SerializedFieldDataConverter;
use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\ResourceModel\Fee;

class UpgradeData
{
    /**
     * @var SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    public function __construct(SerializedFieldDataConverter $fieldDataConverter)
    {
        $this->fieldDataConverter = $fieldDataConverter;
    }

    /**
     * @param \Magento\SalesRule\Setup\UpgradeData $subject
     * @param $result
     * @return mixed
     */
    public function afterConvertSerializedDataToJson(\Magento\SalesRule\Setup\UpgradeData $subject, $result)
    {
        $this->fieldDataConverter->convertSerializedDataToJson(
            Fee::TABLE_NAME,
            FeeInterface::ENTITY_ID,
            [FeeInterface::CONDITIONS_SERIALIZED]
        );

        return $result;
    }
}
