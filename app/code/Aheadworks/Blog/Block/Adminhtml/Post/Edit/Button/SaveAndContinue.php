<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndContinue
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class SaveAndContinue extends Button implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $postId = $this->getPostId();
        if ($postId) {
            $post = $this->postRepository->get($postId);
            if ($post->getStatus() == Status::PUBLICATION) {
                $data = [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit'],
                        ],
                    ],
                    'sort_order' => 40,
                ];
            }
        }
        return $data;
    }
}
