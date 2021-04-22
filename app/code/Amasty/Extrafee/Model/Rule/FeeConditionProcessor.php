<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model\Rule;

use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

/**
 * Extra fee rule conditions and product conditions (actions) processor.
 * Simplified and splited from main data model.
 */
class FeeConditionProcessor extends DataObject
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var Combine
     */
    private $actions;

    /**
     * @var \Magento\Rule\Model\Condition\Combine
     */
    private $conditions;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    private $conditionsFactory;

    /**
     * @var CombineFactory
     */
    private $actionsFactory;

    public function __construct(
        FormFactory $formFactory,
        Json $serializer,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $conditionsFactory,
        CombineFactory $actionsFactory,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->serializer = $serializer;
        $this->conditionsFactory = $conditionsFactory;
        $this->actionsFactory = $actionsFactory;
        parent::__construct($data);
    }

    /**
     * Collect cart items by product conditions.
     * Returned valid items with parents ('getAllItems' quote algorithm)
     *
     * @param Quote $quote
     *
     * @return Item[]
     */
    public function getValidItems(CartInterface $quote)
    {
        if (!$this->hasData('cached_items')) {
            $items = [];
            foreach ($quote->getAllItems() as $item) {
                if (!$item->getChildren() && $this->getActions()->validateByEntityId($item->getProductId())
                ) {
                    if ($item->getParentItem()) {
                        //parent item should be higher in array then child
                        $items[] = $item->getParentItem();
                    }
                    $items[] = $item;
                }
            }
            $this->setData('cached_items', $items);
        }

        return $this->_getData('cached_items');
    }

    /**
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->conditionsFactory->create();
    }

    /**
     * @return Combine
     */
    public function getActionsInstance()
    {
        return $this->actionsFactory->create();
    }

    /**
     * Set rule combine conditions model
     *
     * @param \Magento\Rule\Model\Condition\Combine $conditions
     *
     * @return $this
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Retrieve rule combine conditions model
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditions()
    {
        if (empty($this->conditions)) {
            $this->resetConditions();
        }

        // Load rule conditions if it is applicable
        if ($this->hasConditionsSerialized()) {
            $conditions = $this->getConditionsSerialized();
            if (!empty($conditions)) {
                $conditions = $this->serializer->unserialize($conditions);
                if (is_array($conditions) && !empty($conditions)) {
                    $this->conditions->loadArray($conditions);
                }
            }
            $this->unsConditionsSerialized();
        }

        return $this->conditions;
    }

    /**
     * Set rule actions model
     *
     * @param Combine $actions
     *
     * @return $this
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Retrieve rule actions model
     *
     * @return Combine
     */
    public function getActions()
    {
        if (!$this->actions) {
            $this->resetActions();
        }

        // Load rule actions if it is applicable
        if ($this->hasActionsSerialized()) {
            $actions = $this->getActionsSerialized();
            if (!empty($actions)) {
                $actions = $this->serializer->unserialize($actions);
                if (is_array($actions) && !empty($actions)) {
                    $this->actions->loadArray($actions);
                }
            }
            $this->unsActionsSerialized();
        }

        return $this->actions;
    }

    /**
     * Reset rule combine conditions
     *
     * @param null|\Magento\Rule\Model\Condition\Combine $conditions
     *
     * @return $this
     */
    private function resetConditions($conditions = null)
    {
        if (null === $conditions) {
            $conditions = $this->getConditionsInstance();
        }
        $conditions->setRule($this)->setId('1')->setPrefix('conditions');
        $this->setConditions($conditions);

        return $this;
    }

    /**
     * Reset rule actions
     *
     * @param null|Combine $actions
     *
     * @return $this
     */
    private function resetActions($actions = null)
    {
        if (null === $actions) {
            $actions = $this->getActionsInstance();
        }
        $actions->setRule($this)->setId('1')->setPrefix('actions');
        $this->setActions($actions);

        return $this;
    }

    /**
     * Rule form getter
     *
     * @return Form
     */
    public function getForm()
    {
        if (!$this->form) {
            $this->form = $this->formFactory->create();
        }

        return $this->form;
    }

    /**
     * Initialize rule model data from array
     *
     * @param array $data
     *
     * @return $this
     */
    public function loadPost(array $data)
    {
        $arr = $this->convertFlatToRecursive($data);
        $this->unsConditionsSerialized();
        $this->unsActionsSerialized();
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions([])->loadArray($arr['conditions'][1]);
            $this->setConditionsSerialized($this->serializer->serialize($this->getConditions()->asArray()));
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions([])->loadArray($arr['actions'][1], 'actions');
            $this->setActionsSerialized($this->serializer->serialize($this->getActions()->asArray()));
        }

        return $this;
    }

    /**
     * Set specified data to current rule.
     * Set conditions and actions recursively.
     * Convert dates into \DateTime.
     *
     * @param array $data
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function convertFlatToRecursive(array $data)
    {
        $arr = [];
        foreach ($data as $key => $value) {
            if (($key === 'conditions' || $key === 'actions') && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', (string)$id);
                    $node = &$arr;
                    for ($i = 0, $l = count($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = &$node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                $this->setData($key, $value);
            }
        }

        return $arr;
    }

    /**
     * Validate rule conditions to determine if rule can run
     *
     * @param DataObject $object
     *
     * @return bool
     */
    public function validate(DataObject $object)
    {
        return $this->getConditions()->validate($object);
    }

    /**
     * @param string|null $conditions
     *
     * @return $this
     */
    public function setConditionsSerialized(?string $conditions)
    {
        return $this->setData('conditions_serialized', $conditions);
    }

    /**
     * @param string|null $conditions
     *
     * @return $this
     */
    public function getConditionsSerialized(): ?string
    {
        return $this->_getData('conditions_serialized');
    }

    /**
     * @param string|null $actions
     *
     * @return $this
     */
    public function setActionsSerialized(?string $actions)
    {
        return $this->setData('actions_serialized', $actions);
    }

    /**
     * @return string|null
     */
    public function getActionsSerialized(): ?string
    {
        return $this->_getData('actions_serialized');
    }
}
