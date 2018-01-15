<?php
namespace Magento\Customer\Model\Visitor;

/**
 * Interceptor class for @see \Magento\Customer\Model\Visitor
 */
class Interceptor extends \Magento\Customer\Model\Visitor implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Session\SessionManagerInterface $session, \Magento\Framework\HTTP\Header $httpHeader, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Stdlib\DateTime $dateTime, \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $ignoredUserAgents = array(), array $ignores = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $session, $httpHeader, $scopeConfig, $dateTime, $indexerRegistry, $resource, $resourceCollection, $ignoredUserAgents, $ignores, $data);
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
