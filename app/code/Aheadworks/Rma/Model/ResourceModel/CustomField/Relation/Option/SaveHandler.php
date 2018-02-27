<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\ResourceModel\CustomField\Relation\Option;

use Aheadworks\Rma\Api\Data\CustomFieldOptionInterface;
use Aheadworks\Rma\Api\Data\StoreValueInterface;
use Aheadworks\Rma\Api\Data\CustomFieldInterface;
use Aheadworks\Rma\Model\Source\CustomField\Type;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Rma\Model\ResourceModel\CustomField\Relation\Option
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        if (!$entity->getOptions()
            || ($entity->getOptions() && !in_array($entity->getType(), [Type::MULTI_SELECT, Type::SELECT]))
        ) {
            return $entity;
        }

        $entityId = (int)$entity->getId();
        $connection = $this->getConnection();
        $optionTableName = $this->resourceConnection->getTableName('aw_rma_custom_field_option');

        $optionValuesToInsert = [];
        $optionIdsToRemoveValues = [];
        /** @var CustomFieldOptionInterface $option */
        foreach ($entity->getOptions() as $option) {
            $bind = [
                'sort_order' => $option->getSortOrder(),
                'is_default' => $option->isDefault() ? 1 : 0,
                'enabled' => $option->getEnabled()
            ];
            $optionId = $option->getId();
            if ($optionId) {
                $connection->update($optionTableName, $bind, ['id = ?' => $optionId]);
                $optionIdsToRemoveValues[] = $optionId;
            } else {
                $connection->insert($optionTableName, array_merge($bind, ['field_id' => $entityId]));
                $optionId = $connection->lastInsertId($optionTableName);
            }
            $optionValuesToInsert = array_merge(
                $optionValuesToInsert,
                $this->prepareToUpdateOptionValue($option->getStoreLabels(), $optionId)
            );
        }
        $this->updateOptionValues($optionIdsToRemoveValues, $optionValuesToInsert);

        return $entity;
    }

    /**
     * Prepare to update option value
     *
     * @param StoreValueInterface[] $storeLabels
     * @param int $optionId
     * @return array
     */
    private function prepareToUpdateOptionValue($storeLabels, $optionId)
    {
        $optionsValueToInsert = [];
        /** @var StoreValueInterface $optionStoreLabel */
        foreach ($storeLabels as $optionStoreLabel) {
            if (empty($optionStoreLabel->getValue())) {
                continue;
            }
            $optionsValueToInsert[] = [
                'option_id' => $optionId,
                'store_id' => $optionStoreLabel->getStoreId(),
                'value' => $optionStoreLabel->getValue()
            ];
        }
        return $optionsValueToInsert;
    }

    /**
     * Update option values
     *
     * @param array $optionIds
     * @param array $optionValuesToInsert
     */
    private function updateOptionValues($optionIds, $optionValuesToInsert)
    {
        $connection = $this->getConnection();
        $optionValueTableName = $this->resourceConnection->getTableName('aw_rma_custom_field_option_value');
        foreach ($optionIds as $optionId) {
            $connection->delete($optionValueTableName, ['option_id = ?' => $optionId]);
        }
        if ($optionValuesToInsert) {
            $connection->insertMultiple($optionValueTableName, $optionValuesToInsert);
        }
    }

    /**
     * Retrieve connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(CustomFieldInterface::class)->getEntityConnectionName()
        );
    }
}
