<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\Config\Source\ApplyFeeFor;
use Amasty\Extrafee\Model\Config\Source\Excludeinclude;
use Amasty\Extrafee\Model\Rule\RuleRepository;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Structure\Element\Dependency\Field;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Rule\Block\Actions;
use Magento\Rule\Model\Condition\AbstractCondition;

class Calculation extends Generic implements TabInterface
{
    const FORM_NAME = 'fee_product_condition_form';

    const RULE_ACTIONS_FIELDSET_NAMESPACE = 'product_condition_fieldset';

    /**
     * @var Dependence|null
     */
    private $dependencies;

    /**
     * @var Excludeinclude
     */
    private $excludeincludeSource;

    /**
     * @var ApplyFeeFor
     */
    private $applyForSource;

    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    /**
     * @var Actions
     */
    private $actions;

    /**
     * @var Fieldset
     */
    private $rendererFieldset;

    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Excludeinclude $excludeincludeSource,
        ApplyFeeFor $applyForSource,
        FeeRepositoryInterface $feeRepository,
        Actions $actions,
        Fieldset $rendererFieldset,
        RuleRepository $ruleRepository,
        array $data = []
    ) {
        $this->excludeincludeSource = $excludeincludeSource->setUseDefaultOption(true);
        $this->applyForSource = $applyForSource;
        $this->feeRepository = $feeRepository;
        $this->actions = $actions;
        $this->rendererFieldset = $rendererFieldset;
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
        return __('Calculation');
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

        if ($feeId = $this->getRequest()->getParam('id')) {
            $model = $this->feeRepository->getById($feeId);
        } else {
            $model = $this->feeRepository->create();
        }

        $fieldset = $form->addFieldset(
            'calculation_fieldset',
            ['legend' => __('Percent Fee Calculation'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'discount_in_subtotal',
            'select',
            [
                'name' => 'discount_in_subtotal',
                'label' => __('Include discount in subtotal'),
                'title' => __('Include discount in subtotal'),
                'note' => __(
                    'Select \'Yes\' if you want to calculate the extra fee'
                    . ' based on the prices with discount amounts (only for percent fee type)'
                ),
                'values' => $this->excludeincludeSource->toOptionArray()
            ]
        );

        $fieldset->addField(
            'shipping_in_subtotal',
            'select',
            [
                'name' => 'shipping_in_subtotal',
                'label' => __('Include shipping in subtotal'),
                'title' => __('Include shipping in subtotal'),
                'note' => __(
                    'Select \'Yes\' if you want to calculate the extra fee'
                    . ' based on the prices with shipping costs (only for percent fee type)'
                ),
                'values' => $this->excludeincludeSource->toOptionArray()
            ]
        );

        $this->addConditionsFieldset($model, $form);

        if (!$model->getId()) {
            $model->setData(
                [
                    'discount_in_subtotal' => Excludeinclude::VAR_DEFAULT,
                    'tax_in_subtotal' => Excludeinclude::VAR_DEFAULT,
                    'shipping_in_subtotal' => Excludeinclude::VAR_DEFAULT
                ]
            );
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param FeeInterface $model
     * @param Form $form
     */
    private function addConditionsFieldset($model, $form): void
    {
        $isPerProduct = $form->addFieldset(
            'apply_for',
            [
                'legend' => __('Fee Calculation Applicability')
            ]
        )->addField(
            FeeInterface::IS_PER_PRODUCT,
            'select',
            [
                'name' => FeeInterface::IS_PER_PRODUCT,
                'label' => __('Apply Fees for'),
                'title' => __('Apply Fees for'),

                'values' => $this->applyForSource->toOptionArray()
            ]
        );

        $rule = $this->ruleRepository->getByFee($model);

        $renderer = $this->rendererFieldset
            ->setTemplate('Amasty_Extrafee::fee/product_conditions_fieldset.phtml')
            ->setFieldSetId(self::RULE_ACTIONS_FIELDSET_NAMESPACE)
            ->setNewChildUrl(
                $this->getUrl(
                    '*/*/newActionHtml',
                    ['form_namespace' => self::FORM_NAME, 'form' => self::RULE_ACTIONS_FIELDSET_NAMESPACE]
                )
            );

        $conditionsFieldset = $form->addFieldset(
            self::RULE_ACTIONS_FIELDSET_NAMESPACE,
            [
                'legend' => __(
                    'Only products with attributes selected below will be counted for this'
                    . ' Fee calculation (leave blank for all Items)'
                ),
                'comment' => __(
                    'Percent based Fees are being calculated using eligible products\' Subtotal as a base. '
                    . 'Fixed Fees use eligible products\' quantity while \'Per Product\' is enabled.'
                ),
            ]
        )->setRenderer(
            $renderer
        );

        $conditionsFieldset->addField(
            'actions',
            'text',
            [
                'name' => 'actions',
                'label' => '',
                'title' => '',
                'data-form-part' => self::FORM_NAME,
            ]
        )->setRule(
            $rule
        )->setRenderer(
            $this->actions
        );
        $this->setActionFormName($rule->getActions(), self::RULE_ACTIONS_FIELDSET_NAMESPACE, self::FORM_NAME);

        $this->makeDependence($isPerProduct, $conditionsFieldset);
    }

    /**
     * @param AbstractElement $mainElement
     * @param AbstractElement $dependentElement
     * @param Field|string $values
     */
    private function makeDependence(AbstractElement $mainElement, AbstractElement $dependentElement, $values = '1')
    {
        if (!$this->dependencies) {
            $this->dependencies = $this->getLayout()
                ->createBlock(Dependence::class);
            $this->setChild('form_after', $this->dependencies);
        }

        $this->dependencies->addFieldMap($mainElement->getHtmlId(), $mainElement->getName())
            ->addFieldMap($dependentElement->getHtmlId(), $dependentElement->getName())
            ->addFieldDependence($dependentElement->getName(), $mainElement->getName(), $values);
    }

    /**
     * @param AbstractCondition $actions
     * @param string $fieldsetName
     * @param string $formName
     *
     * @return void
     */
    private function setActionFormName(
        AbstractCondition $actions,
        $fieldsetName,
        $formName
    ) {
        $actions->setFormName($formName);
        $actions->setJsFormObject($fieldsetName);

        if ($actions->getActions() && is_array($actions->getActions())) {
            foreach ($actions->getActions() as $condition) {
                $this->setActionFormName($condition, $fieldsetName, $formName);
            }
        }
    }
}
