<?php

namespace Icube\CustomStyle\Controller\Adminhtml\Cache;

use Magento\Framework\App\Action\Action;
use Magento\Backend\App\Action\Context;

class ClearPage extends Action
{

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Context $context
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $types = array('full_page');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }

        $message = "Clear page cache has been executed, please wait in few minutes.";
        $this->messageManager->addSuccess(__($message));

        return $this->_redirect('customstyle/cache/index');
    }
}
