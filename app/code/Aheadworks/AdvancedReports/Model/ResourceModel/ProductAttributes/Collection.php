<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\ProductAttributes;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\ProductAttributes as ResourceProductAttributes;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Aheadworks\AdvancedReports\Model\Config;
use Aheadworks\AdvancedReports\Model\Filter;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\ProductAttributes
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var bool
     */
    protected $periodBased = true;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Config $config
     * @param Filter\Store $storeFilter
     * @param Filter\CustomerGroup $customerGroupFilter
     * @param Filter\Groupby $groupbyFilter
     * @param Filter\Period $periodFilter
     * @param AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Config $config,
        Filter\Store $storeFilter,
        Filter\CustomerGroup $customerGroupFilter,
        Filter\Groupby $groupbyFilter,
        Filter\Period $periodFilter,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->metadataPool = $metadataPool;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $config,
            $storeFilter,
            $customerGroupFilter,
            $groupbyFilter,
            $periodFilter,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceProductAttributes::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(true));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $rateField = $this->getRateField($addRate);
        return [
            'order_items_count' => 'SUM(COALESCE(main_table.order_items_count, 0))',
            'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
            'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
            'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
            'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
            'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'periodFilter') {
            return $this->addGroupByFilter();
        }
        if ($field == 'attributeFilter') {
            return $this->addAttributeFilter($condition['eq']);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add attribute filter to collection
     *
     * @param [] $conditions
     * @return $this
     */
    public function addAttributeFilter($conditions)
    {
        if ($conditions) {
            $this->getSelect()
                ->joinLeft(
                    ['e' => 'catalog_product_entity'],
                    'main_table.product_id = e.' . $this->getCatalogLinkField(),
                    []
                );

            $conditionSql = '';
            foreach ($conditions as $key => $condition) {
                if ($key > 0) {
                    $conditionSql .= ' ' . $condition['operator'] . ' ';
                }
                $conditionSql .=
                    '(' . $this->getAttributeConditionSql($condition['attribute'], $condition['condition']) . ')';
            }
            if (!empty($conditionSql)) {
                $this->conditionsForGroupBy[] = [
                    'field' => '(' . $conditionSql . ')',
                    'condition' => []
                ];
            }
        }
        return $this;
    }

    /**
     * Get condition sql for the attribute
     *
     * @param string $attributeCode
     * @param [] $condition
     * @return string
     */
    private function getAttributeConditionSql($attributeCode, $condition)
    {
        /* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);

        if ($attribute->getBackend()->isStatic()) {
            $conditionSql = $this->prepareSqlCondition('e.' . $attributeCode, $condition);
        } else {
            $linkField = $this->getCatalogLinkField();
            $table = $attribute->getBackendTable();
            $tableAlias = 'at_' . $attribute->getAttributeCode();
            $tableAliasDefault = '';

            if (!$this->getFlag($tableAlias . '_joined')) {
                $storeConditions = $tableAlias . '.store_id = 0';
                if (!$attribute->isScopeGlobal()) {
                    if ($storeIds = $this->storeFilter->getStoreIds()) {
                        $storeConditions = $tableAlias . '.store_id IN (' . implode($storeIds) . ')';
                        $tableAliasDefault = $tableAlias . '_d';
                        $defaultStoreConditions = $tableAliasDefault . '.store_id = 0';
                    }
                }
                $this->getSelect()
                    ->joinLeft(
                        [$tableAlias => $table],
                        'e.' . $linkField . ' = ' . $tableAlias . '.' . $linkField
                        . ' AND ' . $tableAlias . '.attribute_id = ' . $attribute->getId()
                        . ' AND ' . $storeConditions,
                        []
                    );
                $onDefaultField = '';
                if ($tableAliasDefault) {
                    $this->getSelect()
                        ->joinLeft(
                            [$tableAliasDefault => $table],
                            'e.' . $linkField . ' = ' . $tableAliasDefault . '.' . $linkField
                            . ' AND ' . $tableAliasDefault . '.attribute_id = ' . $attribute->getId()
                            . ' AND ' . $defaultStoreConditions,
                            []
                        );
                    $onDefaultField = $tableAliasDefault . '.value';
                }
                $this->setFlag($tableAlias . '_joined', true);
            }
            $conditionSql = $this->prepareSqlCondition(
                $tableAlias . '.value',
                $condition,
                $onDefaultField
            );
        }
        return $conditionSql;
    }

    /**
     * Retrieve sql condition
     *
     * @param string $field
     * @param [] $condition
     * @param string $condition
     * @return string $onDefaultField
     */
    private function prepareSqlCondition($field, $condition, $onDefaultField = '')
    {
        $prefix = '';
        foreach ($condition as $key => &$value) {
            switch ($key) {
                case 'like':
                case 'nlike':
                    $value = '%' . $value . '%';
                    break;
                case 'in':
                case 'nin':
                    $value = implode(',', array_map('trim', explode(',', $value)));
                    break;
                case 'not_finset':
                    $condition['finset'] = $value;
                    $prefix = 'NOT ';
                    unset($condition['not_finset']);
                    break;
            }
        }
        $conditionSql = $prefix . $this->_getConditionSql(
            $this->getConnection()->quoteIdentifier($field),
            $condition
        );

        if ($onDefaultField) {
            $onDefaultConditionSql = $this->_getConditionSql(
                $this->getConnection()->quoteIdentifier($onDefaultField),
                $condition
            );
            $conditionSql = 'COALESCE(' . $conditionSql . ',' . $prefix . $onDefaultConditionSql . ')';
        }

        return $conditionSql;
    }

    /**
     * Retrieve catalog link field
     *
     * @return string
     */
    private function getCatalogLinkField()
    {
        return $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
    }
}
