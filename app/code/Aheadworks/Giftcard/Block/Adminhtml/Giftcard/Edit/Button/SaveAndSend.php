<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Adminhtml\Giftcard\Edit\Button;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Model\GiftcardRepository;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class SaveAndSend
 *
 * @package Aheadworks\Giftcard\Block\Adminhtml\Giftcard\Edit\Button
 */
class SaveAndSend implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var GiftcardRepository
     */
    private $giftcardRepository;

    /**
     * @param Context $context
     * @param GiftcardRepository $giftcardRepository
     */
    public function __construct(
        Context $context,
        GiftcardRepository $giftcardRepository
    ) {
        $this->context = $context;
        $this->giftcardRepository = $giftcardRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $giftcard = $this->getGiftcard();
        if ($giftcard && $giftcard->getType() == GiftcardType::VALUE_PHYSICAL) {
            return [];
        }

        $id = $giftcard && $giftcard->getId() ? $giftcard->getId() : null;
        $label = $id ? __('Save and Resend Gift Card') : __('Save and Send Gift Card');
        return [
            'label'          => $label,
            'class'          => 'save' . ($id ? '' : ' primary'),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'saveAndSend']],
                'form-role' => 'save',
            ],
            'sort_order'     => $id ? 60 : 70,
        ];
    }

    /**
     * Retrieve gift card id
     *
     * @return GiftcardInterface|null
     */
    public function getGiftcard()
    {
        $id = $this->context->getRequest()->getParam('id');
        if ($id && $this->giftcardRepository->get($id)) {
            return $this->giftcardRepository->get($id);
        }
        return null;
    }
}
