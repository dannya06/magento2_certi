<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

use Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

/**
 * Class Delete
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class Delete extends Button implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $postId = $this->getPostId();
        if ($postId && $this->postRepository->get($postId)) {
            $confirmMessage = __('Are you sure you want to do this?');
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf("deleteConfirm('%s', '%s')", $confirmMessage, $this->getDeleteUrl()),
                'sort_order' => 20
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['post_id' => $this->getPostId()]);
    }
}
