<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab;

use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Customer\Ui\Component\Listing\Column\Group\Options as GroupOptions;

class Filter extends Generic implements TabInterface
{
    /** @var SystemStore  */
    protected $systemStore;

    /** @var GroupOptions  */
    protected $groupOptions;

    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        SystemStore $systemStore,
        GroupOptions $groupOptions,
        FeeRepositoryInterface $feeRepository,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->groupOptions = $groupOptions;
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
        return __('Stores & Customer Groups');
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
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('amasty_extrafee_');

        if ($feeId = $this->getRequest()->getParam('id')) {
            $model = $this->feeRepository->getById($feeId);
        } else {
            $model = $this->feeRepository->create();
        }

        $fieldset = $form->addFieldset(
            'filter_fieldset',
            ['legend' => __('Stores & Customer Groups'), 'class' => 'fieldset-wide']
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                ]
            );

            /** @var Element $renderer */
            $renderer = $this->getLayout()->createBlock(Element::class);
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField('customer_group_id', 'multiselect', [
            'label' => __('Customer Groups'),
            'title' => __('Customer Groups'),
            'name' => 'groups[]',
            'required' => true,
            'values' => $this->groupOptions->toOptionArray(),
        ]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
