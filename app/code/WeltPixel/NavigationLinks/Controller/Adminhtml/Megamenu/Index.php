<?php
namespace WeltPixel\NavigationLinks\Controller\Adminhtml\Megamenu;

class Index extends \WeltPixel\NavigationLinks\Controller\Adminhtml\Megamenu
{
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;
	
	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context, $coreRegistry);
	}
	
	/**
	 * Index action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('WeltPixel Mega Menu Instructions'));
		
		return $resultPage;
	}
}
