<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Aheadworks\Blog\Api\PostRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AbstractMassAction
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * AbstractMassAction constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        PostRepositoryInterface $postRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->postRepository = $postRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $component = $this->filter->getComponent();
            $this->filter->prepareComponent($component);
            $this->filter->applySelectionOnTargetProvider();
            $searchResult = $component->getContext()->getDataProvider()->getSearchResult();
            return $this->massAction($searchResult);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/index');
        }
    }

    /**
     * Performs mass action
     *
     * @param SearchResultsInterface $searchResults
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    abstract protected function massAction(SearchResultsInterface $searchResults);
}
