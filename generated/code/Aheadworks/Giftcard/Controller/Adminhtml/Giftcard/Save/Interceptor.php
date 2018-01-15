<?php
namespace Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\Save;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\Save
 */
class Interceptor extends \Aheadworks\Giftcard\Controller\Adminhtml\Giftcard\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Giftcard\Api\GiftcardRepositoryInterface $giftcardRepository, \Aheadworks\Giftcard\Api\GiftcardManagementInterface $giftcardManagement, \Aheadworks\Giftcard\Model\Config $config, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Aheadworks\Giftcard\Api\Data\GiftcardInterfaceFactory $giftcardDataFactory)
    {
        $this->___init();
        parent::__construct($context, $giftcardRepository, $giftcardManagement, $config, $dateTime, $localeDate, $dataObjectHelper, $dataPersistor, $giftcardDataFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
