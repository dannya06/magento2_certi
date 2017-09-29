<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Schedule
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class Schedule extends Button implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Schedule Post'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'save']
                ],
                'form-role' => 'save',
            ],
            'style' => 'display:none;',
            'sort_order' => 50,
        ];
    }
}
