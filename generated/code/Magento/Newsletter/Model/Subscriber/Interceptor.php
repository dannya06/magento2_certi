<?php
namespace Magento\Newsletter\Model\Subscriber;

/**
 * Interceptor class for @see \Magento\Newsletter\Model\Subscriber
 */
class Interceptor extends \Magento\Newsletter\Model\Subscriber implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Newsletter\Helper\Data $newsletterData, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $newsletterData, $scopeConfig, $transportBuilder, $storeManager, $customerSession, $customerRepository, $customerAccountManagement, $inlineTranslation, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe($email)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'subscribe');
        if (!$pluginInfo) {
            return parent::subscribe($email);
        } else {
            return $this->___callPlugins('subscribe', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsubscribe');
        if (!$pluginInfo) {
            return parent::unsubscribe();
        } else {
            return $this->___callPlugins('unsubscribe', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribeCustomerById($customerId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'subscribeCustomerById');
        if (!$pluginInfo) {
            return parent::subscribeCustomerById($customerId);
        } else {
            return $this->___callPlugins('subscribeCustomerById', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribeCustomerById($customerId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsubscribeCustomerById');
        if (!$pluginInfo) {
            return parent::unsubscribeCustomerById($customerId);
        } else {
            return $this->___callPlugins('unsubscribeCustomerById', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save();
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }
}
