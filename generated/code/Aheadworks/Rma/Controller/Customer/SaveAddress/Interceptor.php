<?php
namespace Aheadworks\Rma\Controller\Customer\SaveAddress;

/**
 * Interceptor class for @see \Aheadworks\Rma\Controller\Customer\SaveAddress
 */
class Interceptor extends \Aheadworks\Rma\Controller\Customer\SaveAddress implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aheadworks\Rma\Api\RequestRepositoryInterface $requestRepository, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Aheadworks\Rma\Api\RequestManagementInterface $requestManagement, \Aheadworks\Rma\Model\Request\PostDataProcessor\Composite $requestPostDataProcessor, \Aheadworks\Rma\Api\Data\RequestInterfaceFactory $requestFactory)
    {
        $this->___init();
        parent::__construct($context, $requestRepository, $customerSession, $formKeyValidator, $dataObjectHelper, $requestManagement, $requestPostDataProcessor, $requestFactory);
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
