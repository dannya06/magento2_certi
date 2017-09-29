<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @inheritdoc
     */
    protected function massAction(SearchResultsInterface $searchResults)
    {
        $deletedRecords = 0;
        foreach ($searchResults->getItems() as $post) {
            $this->postRepository->delete($post);
            $deletedRecords++;
        }
        if ($deletedRecords) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $deletedRecords));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
