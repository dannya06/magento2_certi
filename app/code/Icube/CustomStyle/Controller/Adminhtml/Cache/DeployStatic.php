<?php

namespace Icube\CustomStyle\Controller\Adminhtml\Cache;

use Magento\Framework\App\Action\Action;
use Magento\Backend\App\Action\Context;

class DeployStatic extends Action
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $command = "echo deploy > /home/mage2user/site/current/pub/media/deploy1.flag";
        $data = "<pre>".shell_exec($command)."</pre>";

        $message = "Regenerate static files has been executed, your site will be in maintenance mode in few minutes.";
        $this->messageManager->addSuccess(__($message));

        return $this->_redirect('customstyle/cache/index');
    }
}
