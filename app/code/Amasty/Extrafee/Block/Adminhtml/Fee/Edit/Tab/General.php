<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\Fee\Source\FrontendType;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class General extends Generic implements TabInterface
{
    /** @var Yesno */
    protected $yesno;

    /** @var FrontendType */
    protected $frontendType;

    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesno,
        FrontendType $frontendType,
        FeeRepositoryInterface $feeRepository,
        array $data = []
    ) {
        $this->yesno = $yesno;
        $this->frontendType = $frontendType;
        $this->feeRepository = $feeRepository;
        return parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('General');
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
            'general_fieldset',
            ['legend' => __('General'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('fee_id', 'hidden', ['name' => 'fee_id']);
        }

        $fieldset->addField(
            FeeInterface::NAME,
            'text',
            [
                'name' => FeeInterface::NAME,
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            FeeInterface::ENABLED,
            'select',
            [
                'name' => FeeInterface::ENABLED,
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'values' => $this->yesno->toOptionArray()
            ]
        );

        $fieldset->addField(
            FeeInterface::IS_REQUIRED,
            'select',
            [
                'name' => FeeInterface::IS_REQUIRED,
                'label' => __('Mandatory to select'),
                'title' => __('Mandatory to select'),
                'values' => $this->yesno->toOptionArray()
            ]
        );

        $fieldset->addField(
            FeeInterface::IS_ELIGIBLE_REFUND,
            'select',
            [
                'name' => FeeInterface::IS_ELIGIBLE_REFUND,
                'label' => __('Eligible for Refund'),
                'title' => __('Eligible for Refund'),
                'values' => $this->yesno->toOptionArray()
            ]
        );

        $fieldset->addField(
            FeeInterface::FRONTEND_TYPE,
            'select',
            [
                'name' => FeeInterface::FRONTEND_TYPE,
                'label' => __('Type'),
                'title' => __('Type'),
                'values' => $this->frontendType->toOptionArray()
            ]
        );

        $fieldset->addField(
            FeeInterface::SORT_ORDER,
            'text',
            [
                'name' => FeeInterface::SORT_ORDER,
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'class' => 'validate-number',
            ]
        );

        $fieldset->addField(
            FeeInterface::DESCRIPTION,
            'textarea',
            [
                'name' => FeeInterface::DESCRIPTION,
                'label' => __('Description'),
                'title' => __('Description')
            ]
        );

        $form->setValues($model->getData());
        $form->addValues(['fee_id' => $model->getId()]);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
