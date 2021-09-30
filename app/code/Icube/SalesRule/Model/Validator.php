<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\SalesRule\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Helper\CartFixedDiscount;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

/**
 * SalesRule Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @method mixed getCouponCode()
 * @method Validator setCouponCode($code)
 * @method mixed getWebsiteId()
 * @method Validator setWebsiteId($id)
 * @method mixed getCustomerGroupId()
 * @method Validator setCustomerGroupId($id)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Validator extends \Magento\SalesRule\Model\Validator
{
    /**
     * Rule source collection
     *
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    protected $_rules;

    /**
     * Defines if method \Magento\SalesRule\Model\Validator::reset() wasn't called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @var bool
     */
    protected $_isFirstTimeResetRun = true;

    /**
     * Information about item totals for rules
     *
     * @var array
     */
    protected $_rulesItemTotals = [];

    /**
     * Skip action rules validation flag
     *
     * @var bool
     */
    protected $_skipActionsValidation = false;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data|null
     */
    protected $_catalogData = null;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    /**
     * @var \Magento\SalesRule\Model\RulesApplier
     */
    protected $rulesApplier;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Validator\Pool
     */
    protected $validators;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Counter is used for assigning temporary id to quote address
     *
     * @var int
     */
    protected $counter = 0;

    /**
     * @var CartFixedDiscount
     */
    private $cartFixedDiscountHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param Utility $utility
     * @param RulesApplier $rulesApplier
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Validator\Pool $validators
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param CartFixedDiscount|null $cartFixedDiscount
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CollectionFactory $collectionFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\RulesApplier $rulesApplier,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\SalesRule\Model\Validator\Pool $validators,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ?CartFixedDiscount $cartFixedDiscount = null
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_catalogData = $catalogData;
        $this->validatorUtility = $utility;
        $this->rulesApplier = $rulesApplier;
        $this->priceCurrency = $priceCurrency;
        $this->validators = $validators;
        $this->messageManager = $messageManager;
        $this->cartFixedDiscountHelper = $cartFixedDiscount ?:
            ObjectManager::getInstance()->get(CartFixedDiscount::class);
    }

    
    /**
     * Calculate quote totals for each rule and save results
     *
     * @param mixed $items
     * @param Address $address
     * @return $this
     * @throws \Zend_Validate_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function initTotals($items, Address $address)
    {
        $address->setCartFixedRules([]);

        if (!$items) {
            return $this;
        }

        /** @var Rule $rule */
        foreach ($this->_getRules($address) as $rule) {
            if (\Magento\SalesRule\Model\Rule::CART_FIXED_ACTION == $rule->getSimpleAction()
                && $this->validatorUtility->canProcessRule($rule, $address)
            ) {
                $ruleTotalItemsPrice = 0;
                $ruleTotalBaseItemsPrice = 0;
                $validItemsCount = 0;
                $bundleCount = 0;
                $qtyOptionsx = 0;

                foreach ($items as $item) {
                    //Skipping child items to avoid double calculations
                    if (!$this->isValidItemForRule($item, $rule)) {
                        continue;
                    }
                    $qty = $this->validatorUtility->getItemQty($item, $rule);
                    $ruleTotalItemsPrice += $this->getItemPrice($item) * $qty;
                    $ruleTotalBaseItemsPrice += $this->getItemBasePrice($item) * $qty;
                    $validItemsCount++;
                    if(count($item->getData("qty_options")) > 0){
                        $bundleCount++;
                        $qtyOptions = count($item->getData("qty_options"));
                        $qtyOptionsx += $qtyOptions;
                        $validItemsCount = $validItemsCount + $qtyOptionsx;
                    }
                }

                $this->_rulesItemTotals[$rule->getId()] = [
                    'items_price' => $ruleTotalItemsPrice,
                    'base_items_price' => $ruleTotalBaseItemsPrice,
                    'items_count' => $validItemsCount,
                ];
            }
        }

        return $this;
    }

    /**
     * Determine if quote item is valid for a given sales rule
     *
     * @param AbstractItem $item
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return bool
     */
    private function isValidItemForRule(AbstractItem $item, \Magento\SalesRule\Model\Rule $rule)
    {
        if ($item->getParentItemId()) {
            return false;
        }
        if ($item->getParentItem()) {
            return false;
        }
        if (!$rule->getActions()->validate($item)) {
            return false;
        }
        if (!$this->canApplyDiscount($item)) {
            return false;
        }
        return true;
    }

}
