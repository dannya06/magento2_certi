<?php
namespace Magento\SalesRule\Model\Rule;

/**
 * Interceptor class for @see \Magento\SalesRule\Model\Rule
 */
class Interceptor extends \Magento\SalesRule\Model\Rule implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\SalesRule\Model\CouponFactory $couponFactory, \Magento\SalesRule\Model\Coupon\CodegeneratorFactory $codegenFactory, \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory, \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF, \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array(), \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory = null, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory = null, \Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $localeDate, $couponFactory, $codegenFactory, $condCombineFactory, $condProdCombineF, $couponCollection, $storeManager, $resource, $resourceCollection, $data, $extensionFactory, $customAttributeFactory, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterSave');
        if (!$pluginInfo) {
            return parent::afterSave();
        } else {
            return $this->___callPlugins('afterSave', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($modelId, $field = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'load');
        if (!$pluginInfo) {
            return parent::load($modelId, $field);
        } else {
            return $this->___callPlugins('load', func_get_args(), $pluginInfo);
        }
    }
}
