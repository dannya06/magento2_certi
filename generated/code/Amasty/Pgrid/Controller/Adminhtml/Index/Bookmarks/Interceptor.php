<?php
namespace Amasty\Pgrid\Controller\Adminhtml\Index\Bookmarks;

/**
 * Interceptor class for @see \Amasty\Pgrid\Controller\Adminhtml\Index\Bookmarks
 */
class Interceptor extends \Amasty\Pgrid\Controller\Adminhtml\Index\Bookmarks implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository, \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement, \Magento\Ui\Api\Data\BookmarkInterfaceFactory $bookmarkFactory, \Magento\Authorization\Model\UserContextInterface $userContext, \Magento\Framework\Json\DecoderInterface $jsonDecoder, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\View\Element\UiComponentFactory $factory)
    {
        $this->___init();
        parent::__construct($context, $bookmarkRepository, $bookmarkManagement, $bookmarkFactory, $userContext, $jsonDecoder, $jsonEncoder, $resultJsonFactory, $factory);
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
