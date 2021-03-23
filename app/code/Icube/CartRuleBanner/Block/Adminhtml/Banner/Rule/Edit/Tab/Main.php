<?php

namespace Icube\CartRuleBanner\Block\Adminhtml\Banner\Rule\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Main extends Generic implements TabInterface
{

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Generic
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Rule Name'), 'title' => __('Rule Name'), 'required' => true]
        );

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cmsBlocks = $objectManager->get("Magento\Cms\Model\ResourceModel\Block\CollectionFactory")->create()->load()->toOptionArray();

        foreach ($cmsBlocks as $cmsBlock) {
            $values[] = array(
                'value'     => $cmsBlock['value'],
                'label'     => $cmsBlock['label']
            );
        }
        
        array_unshift($values, ['value' => '', 'label' => __('Please select a static block.')]);
        $fieldset->addField(
            'cms_block_id',
            'select',
            [
                'label' => __('CMS Block'),
                'title' => __('CMS Block'),
                'name' => 'cms_block_id',
                'required' => true,
                'values' => $values
            ]
        );

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        $this->_eventManager->dispatch('adminhtml_banner_rule_edit_tab_main_prepare_form', ['form' => $form]);

        return parent::_prepareForm();
    }
}