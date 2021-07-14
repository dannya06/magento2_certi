<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab;

use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab\Option\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class Option extends Generic implements TabInterface
{
    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FeeRepositoryInterface $feeRepository,
        array $data = []
    ) {
        $this->feeRepository = $feeRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Options');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        if ($feeId = $this->getRequest()->getParam('id')) {
            $model = $this->feeRepository->getById($feeId);
        } else {
            $model = $this->feeRepository->create();
        }

        $fieldset = $form->addFieldset(
            'option_fieldset',
            ['legend' => __('Options'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'options',
            'text',
            [
                'name' => 'options',
                'label' => __('Options'),
                'title' => __('Options')
            ]
        );

        $form->getElement(
            'options'
        )->setRenderer(
            $this->getLayout()->createBlock(Field::class)
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
