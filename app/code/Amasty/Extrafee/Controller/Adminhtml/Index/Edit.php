<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab\Calculation;
use Amasty\Extrafee\Model\FeeRepository;
use Amasty\Extrafee\Model\Rule\RuleRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Edit extends Index
{
    /**
     * @var FeeRepository
     */
    private $feeRepository;

    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    public function __construct(
        Context $context,
        FeeRepository $feeRepository,
        RuleRepository $ruleRepository
    ) {
        parent::__construct($context);
        $this->feeRepository = $feeRepository;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @return Page
     */
    public function execute()
    {
        if ($feeId = $this->getRequest()->getParam('id')) {
            $fee = $this->feeRepository->getById($feeId);
        } else {
            $fee = $this->feeRepository->create();
        }
        $rule = $this->ruleRepository->getByFee($fee);

        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $rule->getActions()->setJsFormObject(Calculation::RULE_ACTIONS_FIELDSET_NAMESPACE);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Extrafee::fee_manage');
        $this->prepareDefaultTitle($resultPage);
        $resultPage->setActiveMenu('Magento_Customer::fee');

        if ($fee->getId()) {
            $resultPage->getConfig()->getTitle()->prepend($fee->getName());
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Fee'));
        }

        return $resultPage;
    }
}
