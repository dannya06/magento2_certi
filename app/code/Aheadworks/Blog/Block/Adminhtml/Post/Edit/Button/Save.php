<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Save
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class Save extends Button implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $hidden = false;
        if ($postId = $this->getPostId()) {
            $post = $this->postRepository->get($postId);
            if ($post->getStatus() !== Status::PUBLICATION) {
                $hidden = true;
            }
        } else {
            $hidden = true;
        }
        return [
            'label' => __('Save'),
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
