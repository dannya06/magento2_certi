<?php

namespace Icube\CartRuleBanner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const MODULE_NAME = 'icube_cart_rule_banner/';
    const XML_PATH_IS_ENABLED = 'general/enable';

    protected $_customerSession;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->_utility = $utility;
        $this->_collectionFactory = $collectionFactory;
        $this->_checkoutSession = $checkoutSession;
    }

    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue(self::MODULE_NAME.$path, ScopeInterface::SCOPE_STORE);
    }

    public function isEnabled()
    {
        return (bool) $this->getModuleConfig(self::XML_PATH_IS_ENABLED);
    }

    public function getCustomerGroupId()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer()->getGroupId();
        } else {
            return 0;
        }
    }

    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    public function getRulesCollection()
    {
        $websiteId = $this->getWebsiteId();
        $customerGroupId = $this->getCustomerGroupId();

        return $this->_collectionFactory->create()
                ->addWebsiteGroupDateFilter($websiteId, $customerGroupId)
                ->addAllowedSalesRulesFilter();
    }

    public function getBannerRulesCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get("Icube\CartRuleBanner\Model\ResourceModel\Rule\CollectionFactory")->create();
    }

    public function getValidBanner($quote)
    {
        $address = $quote->getShippingAddress();
        $rules = $this->getRulesCollection();
        $ruleArray = [];

        $items = $quote->getAllVisibleItems();

        foreach ($rules as $rule) {
            $bannerRules = $this->getBannerRulesCollection()->addFilter('rule_id', $rule->getCartRuleBannerId());

            foreach ($bannerRules as $bannerRule) {
                $bannerValidate = false;

                foreach ($items as $item) {
                    if ($bannerValidate = $bannerRule->getConditions()->validate($item)) {
                        break;
                    }
                }

                if ($bannerValidate) {
                    $ruleArray[] = [
                        'cms_block' => $bannerRule->getCmsBlockId(),
                    ];
                }
            }
        }

        return $ruleArray;
    }

    public function getListArray()
    {
        $quote = $this->_checkoutSession->getQuote();
        return $this->getValidBanner($quote);
    }

    public function getBlock()
    {
        $ruleArray = $this->getListArray();
        $valueContent[] = '';
        if (empty(!$ruleArray)) {
            foreach ($ruleArray as $rule) {
                $valueContent[] = $this->layout
                    ->createBlock('Magento\Cms\Block\Block')
                    ->setBlockId($rule['cms_block'])
                    ->toHtml();
            }
        }
        return $valueContent;
    }
}
