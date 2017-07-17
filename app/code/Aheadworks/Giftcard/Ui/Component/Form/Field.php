<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Form;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Field
 *
 * @package Aheadworks\Giftcard\Ui\Component\Form
 */
class Field extends \Magento\Ui\Component\Form\Field
{
    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GiftcardRepositoryInterface $giftcardRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->giftcardRepository = $giftcardRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $giftcard = $this->getGiftcard();
        $giftcardId = $giftcard && $giftcard->getId() ? $giftcard->getId() : null;
        $giftcardTypePhysical = $giftcard && $giftcard->getType() == GiftcardType::VALUE_PHYSICAL ? true : false;

        if ((isset($config['visibleIsSetGcId']) && !$config['visibleIsSetGcId'] && $giftcardId) ||
            (isset($config['visibleIsSetGcId']) && $config['visibleIsSetGcId'] && !$giftcardId) ||
            (isset($config['visibleOnPhysicalGc']) && !$config['visibleOnPhysicalGc'] && $giftcardTypePhysical)
        ) {
            $config['componentDisabled'] = true;
        }
        $this->setData('config', $config);
    }

    /**
     * Retrieve current gift card
     *
     * @return GiftcardInterface|null
     */
    public function getGiftCard()
    {
        $giftcardId = $this->getContext()->getRequestParam(
            $this->getContext()->getDataProvider()->getRequestFieldName(),
            null
        );
        try {
            return $this->giftcardRepository->get($giftcardId);
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }
}
