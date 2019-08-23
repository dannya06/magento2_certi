<?php
namespace Icube\GuestCheckout\Observer;
 use Magento\Framework\Event\ObserverInterface;
 class Checkuser implements ObserverInterface
{
    private $responseFactory;
    protected $customerSession;
     /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;
    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Customer\Model\SessionFactory $customerSession
    ) {
        $this->customerSession = $customerSession->create();
        $this->responseFactory = $responseFactory;
        $this->url = $url;
    }
 	public function execute(\Magento\Framework\Event\Observer $observer)
	{
        if(!$this->customerSession->isLoggedIn()) {
            $redirectionUrl = $this->url->getUrl('customer/account');
            $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
            exit();
        }
 	}
}