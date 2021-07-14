<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Amasty\Extrafee\Model\Rule\FeeConditionProcessorFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\InputException;
use Magento\Rule\Model\Condition\ConditionInterface;

class NewConditionHtml extends Index
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

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        if (!is_a($type, ConditionInterface::class, true)) {
            throw new InputException(__('Conditions class invalid'));
        }

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->ruleFactory->create())
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        $model->setJsFormObject($this->getRequest()->getParam('form'));
        $html = $model->asHtmlRecursive();

        $this->getResponse()->setBody($html);
    }
}
