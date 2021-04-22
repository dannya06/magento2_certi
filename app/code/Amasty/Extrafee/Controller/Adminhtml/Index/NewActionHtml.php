<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Amasty\Extrafee\Model\Rule\FeeConditionProcessorFactory;
use Magento\Backend\App\Action\Context;
use Magento\CatalogRule\Model\Rule\Condition\Product;
use Magento\Framework\Exception\InputException;
use Magento\Rule\Model\Condition\ConditionInterface;

class NewActionHtml extends Index
{
    /**
     * @var FeeConditionProcessorFactory
     */
    private $ruleFactory;

    public function __construct(
        Context $context,
        FeeConditionProcessorFactory $ruleFactory
    ) {
        parent::__construct($context);
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * New action html action
     *
     * @return void
     * @throws InputException
     */
    public function execute()
    {
        $id = (string)$this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        if (!is_a($type, ConditionInterface::class, true)) {
            throw new InputException(__('Conditions class invalid'));
        }

        /** @var Product|ConditionInterface $model */
        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->ruleFactory->create())
            ->setPrefix('actions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        $model->setJsFormObject($this->getRequest()->getParam('form'));
        $model->setFormName($this->getRequest()->getParam('form_namespace'));
        $html = $model->asHtmlRecursive();

        $this->getResponse()->setBody($html);
    }
}
