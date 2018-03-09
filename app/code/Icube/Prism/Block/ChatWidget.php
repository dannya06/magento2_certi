<?php
namespace Icube\Prism\Block;

use \Magento\Customer\Model\Session;
 
class ChatWidget extends \Magento\Framework\View\Element\Template
{
    /**
     * @var UrlInterface
     */
    private $urlinterface;

     /**
     * @var Session
     */
    private $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->urlinterface = $context->getUrlBuilder();
    }

    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }
}