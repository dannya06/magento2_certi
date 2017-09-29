<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Publish
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class Publish extends Button implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $hidden = true;
        if ($postId = $this->getPostId()) {
            $post = $this->postRepository->get($postId);
            if ($post->getStatus() !== Status::PUBLICATION) {
                $hidden = false;
            }
        } else {
            $hidden = false;
        }
        return [
            'label' => __('Publish Post'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'save']
                ],
                'form-role' => 'save',
            ],
            'style' => $hidden ? 'display:none;' : '',
            'sort_order' => 50,
        ];
    }
}
