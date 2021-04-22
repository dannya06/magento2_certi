<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab;

use Amasty\Extrafee\Model\FeeRepository;
use Amasty\Extrafee\Model\Rule\RuleRepository;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as BlockConditions;

class Condition extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var BlockConditions
     */
    protected $blockConditions;

    /**
     * @var FeeRepository
     */
    protected $feeRepository;

    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Fieldset $rendererFieldset,
        BlockConditions $blockConditions,
        FeeRepository $feeRepository,
        RuleRepository $ruleRepository,
        array $data = []
    ) {
        $this->rendererFieldset = $rendererFieldset;
        $this->blockConditions = $blockConditions;
        $this->feeRepository = $feeRepository;
        $this->ruleRepository = $ruleRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Conditions');
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

        $form->setHtmlIdPrefix('rule_');

        if ($feeId = $this->getRequest()->getParam('id')) {
            $fee = $this->feeRepository->getById($feeId);
        } else {
            $fee = $this->feeRepository->create();
        }

        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('amasty_extrafee/index/newConditionHtml/form/rule_conditions_fieldset')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Conditions (don\'t add conditions if need all products)')]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions'), 'required' => true]
        )->setRule(
            $this->ruleRepository->getByFee($fee)
        )->setRenderer(
            $this->blockConditions
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
