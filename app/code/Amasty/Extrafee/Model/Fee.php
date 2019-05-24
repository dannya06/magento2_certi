<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Helper\Data as ExtrafeeHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;

class Fee extends AbstractModel implements FeeInterface, IdentityInterface
{
    /**
     * Frontend types
     */
    const FRONTEND_TYPE_CHECKBOX = 'checkbox';
    const FRONTEND_TYPE_DROPDOWN = 'dropdown';
    const FRONTEND_TYPE_RADIO = 'radio';

    /**
     * Price types
     */
    const PRICE_TYPE_FIXED = 'fixed';
    const PRICE_TYPE_PERCENT = 'percent';

    /**
     * Fee cache tag
     */
    const CACHE_TAG = 'amasty_extrafee_fee';

    /** @var ExtrafeeHelper  */
    protected $extrafeeHelper;

    /**
     * @var Tax
     */
    private $tax;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtrafeeHelper $extrafeeHelper,
        Tax $tax,
        PriceCurrencyInterface $priceCurrency,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->extrafeeHelper = $extrafeeHelper;
        $this->tax = $tax;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Extrafee\Model\ResourceModel\Fee::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Get enabled
     * @return bool|null
     */
    public function getEnabled()
    {
        return parent::getData(self::ENABLED);
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return parent::getData(self::DESCRIPTION);
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return parent::getData(self::OPTIONS);
    }

    /**
     * Get type
     * @return string
     */
    public function getFrontendType()
    {
        return parent::getData(self::FRONTEND_TYPE);
    }

    /**
     * Get current value
     * @return string
     */
    public function getCurrentValue()
    {
        return parent::getData(self::CURRENT_VALUE);
    }

    /**
     * @return mixed
     */
    public function getDiscountInSubtotal()
    {
        $value = parent::getData(self::DISCOUNT_IN_SUBTOTAL);

        if ($value === \Amasty\Extrafee\Model\Config\Source\Excludeinclude::VAR_DEFAULT) {
            $value = $this->extrafeeHelper->getScopeValue(
                'calculation/discount_in_subtotal'
            );
        }

        return $value;
    }

    /**
     * @return mixed
     */
    public function getTaxInSubtotal()
    {
        $value = parent::getData(self::TAX_IN_SUBTOTAL);

        if ($value === \Amasty\Extrafee\Model\Config\Source\Excludeinclude::VAR_DEFAULT) {
            $value = $this->extrafeeHelper->getScopeValue(
                'calculation/tax_in_subtotal'
            );
        }

        return $value;
    }

    /**
     * @return mixed
     */
    public function getShippingInSubtotal()
    {
        $value = parent::getData(self::SHIPPING_IN_SUBTOTAL);

        if ($value === \Amasty\Extrafee\Model\Config\Source\Excludeinclude::VAR_DEFAULT) {
            $value = $this->extrafeeHelper->getScopeValue(
                'calculation/shipping_in_subtotal'
            );
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getConditionsSerialized()
    {
        return parent::getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * @return int|mixed
     */
    public function getSortOrder()
    {
        return parent::getData(self::SORT_ORDER);
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return parent::getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * @return string[]
     */
    public function getStoreId()
    {
        return parent::getData(self::STORE_ID);
    }

    /**
     * @return string
     */
    public function getBaseOptions()
    {
        return $this->getData(self::BASE_OPTIONS);
    }

    /**
     * @param $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        return $this->setData(self::ENABLED, $enabled);
    }

    /**
     * @param string $description
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @param string $name
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @param array $options
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setOptions($options)
    {
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @param array $options
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setBaseOptions($options)
    {
        return $this->setData(self::BASE_OPTIONS, $options);
    }

    /**
     * @param string $frontendType
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setFrontendType($frontendType)
    {
        return $this->setData(self::FRONTEND_TYPE, $frontendType);
    }

    /**
     * @param mixed $currentValue
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setCurrentValue($currentValue)
    {
        return $this->setData(self::CURRENT_VALUE, $currentValue);
    }

    /**
     * @param mixed $discountInSubtotal
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setDiscountInSubtotal($discountInSubtotal)
    {
        return $this->setData(self::DISCOUNT_IN_SUBTOTAL, $discountInSubtotal);
    }

    /**
     * @param mixed $taxInSubtotal
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setTaxInSubtotal($taxInSubtotal)
    {
        return $this->setData(self::TAX_IN_SUBTOTAL, $taxInSubtotal);
    }

    /**
     * @param mixed $shippingInSubtotal
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function setShippingInSubtotal($shippingInSubtotal)
    {
        return $this->setData(self::SHIPPING_IN_SUBTOTAL, $shippingInSubtotal);
    }

    /**
     * @param string $conditionsSerialized
     * @return $this|FeeInterface
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @param int $sortOrder
     * @return $this|FeeInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @param int $entityId
     * @return $this|FeeInterface
     */
    public function setId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @param array $storeId
     * @return $this|FeeInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @param array $groupId
     * @return $this|FeeInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $groupId);
    }

    /**
     * @return \Amasty\Extrafee\Api\Data\FeeInterface
     */
    public function loadOptions()
    {
        return $this->getResource()->loadOptions($this);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return int|float
     */
    protected function getBaseQuoteTotal(\Magento\Quote\Model\Quote $quote)
    {
        $baseQuoteTotals = 0;
        /** @var \Magento\Quote\Model\Quote\Item[] $items */
        $items = $quote->getAllItems();

        if (empty($items)) {
            return $baseQuoteTotals;
        }

        foreach ($items as $item) {
            $baseQuoteTotals += $item->getBasePrice();
            $baseQuoteTotals *= $item->getQty();

            if ($this->getTaxInSubtotal()) {
                $baseQuoteTotals += $item->getBaseTaxAmount() - $item->getBaseDiscountTaxCompensation();
            }

            if ($this->getDiscountInSubtotal()) {
                $baseQuoteTotals -= $item->getBaseDiscountAmount();
            }
        }

        if ($this->getShippingInSubtotal() && !$quote->isVirtual()) {
            $baseQuoteTotals += $quote->getShippingAddress()->getBaseShippingAmount();
        }

        return $baseQuoteTotals;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function fetchBaseOptions(
        \Magento\Quote\Model\Quote $quote
    ) {
        $options = [];
        $storeId = $quote->getStoreId();
        $baseQuoteTotals = null;
        $rate = $quote->getBaseToQuoteRate();
        $taxRate = $this->tax->getTaxRate($quote);

        foreach ($this->getOptions() as $item) {
            $basePrice = $this->priceToFloat($item['price']);
            if ($item['price_type'] === self::PRICE_TYPE_PERCENT) {
                if ($baseQuoteTotals === null) {
                    $baseQuoteTotals = $this->getBaseQuoteTotal($quote);
                }
                $basePrice *= $baseQuoteTotals / 100;
            }
            $price = $this->priceCurrency->convertAndRound($basePrice);

             /**
             * icube custom
             */
            // $logger->info($this->getName());
            if($this->getName() == 'Unique Code Bank Transfer') {
                $unicode = mt_rand(1,500);    
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->create('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $sql = "Select * FROM amasty_extrafee_quote where quote_id = ".$quote->getId()." and option_id = ".$item['entity_id'];
                $result = $connection->fetchRow($sql);
                if ($result['fee_id'] && $result['fee_amount']>0) {
                    $basePrice = $result['fee_amount'];
                }else{
                    $basePrice = $unicode;  
                }
                $price = $basePrice * $rate;
            }
            /**
             * end icube custom
             */
            
            /**
             * apply tax class from module settings
             */
            $tax = 0;
            $baseTax = 0;
            if ($taxRate) {
                $tax += $price * $taxRate / 100;
                $baseTax += $basePrice * $taxRate / 100;
            }

            $options[] = [
                'index' => $item['entity_id'],
                'price' => $price,
                'base_price' => $basePrice,
                'tax' => $tax,
                'base_tax' => $baseTax,
                'default' => $item['default'],
                'label' => $this->getOptionLabel($storeId, $item['options'])
            ];
        }

        return $options;
    }

    /**
     * @param $storeId
     * @param array $values
     * @return string
     */
    protected function getOptionLabel($storeId, array $values)
    {
        $defaultLabel = array_key_exists(0, $values) ? $values[0] : '';

        return array_key_exists($storeId, $values) && $values[$storeId] !== '' ?
            $values[$storeId] :
            $defaultLabel;
    }

    /**
     * @param $storeId
     * @param $optionId
     * @return string
     */
    public function getStoreOptionLabel($storeId, $optionId)
    {
        $item = $this->getOption($optionId);

        return array_key_exists('options', $item) ?
            $this->getOptionLabel($storeId, $item['options']) :
            '';
    }

    /**
     * @param $price
     * @return float
     */
    protected function priceToFloat($price)
    {
        // convert "," to "."
        $price = str_replace(',', '.', $price);
        // remove everything except numbers and dot "."
        $price = preg_replace("/[^0-9\.]/", "", $price);
        // remove all seperators from first part and keep the end
        $price = str_replace('.', '', substr($price, 0, -3)) . substr($price, -3);
        // return float
        return (float) $price;
    }

    /**
     * @param $optionId
     * @return array
     */
    public function getOption($optionId)
    {
        $ret = [];
        foreach ($this->getOptions() as $idx => $item) {
            if ($item['entity_id'] === $optionId) {
                $ret = $item;
                break;
            }
        }
        return $ret;
    }

    /**
     * @return array
     */
    public function getOptionsIds()
    {
        $ids = [];
        foreach ($this->getOptions() as $idx => $item) {
            $ids[] = $item['entity_id'];
        }
        return $ids;
    }
}
