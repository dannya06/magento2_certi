<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Controller\Adminhtml\Index;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\Config\Source\ApplyFeeFor;
use Amasty\Extrafee\Model\FeeRepository;
use Amasty\Extrafee\Model\Rule\FeeConditionProcessorFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\FormData;

class Save extends Index
{
    /**
     * @var FeeRepository
     */
    private $feeRepository;

    /**
     * @var FeeConditionProcessorFactory
     */
    private $ruleFactory;

    /**
     * @var FormData
     */
    private $formDataSerializer;

    public function __construct(
        Action\Context $context,
        FeeRepository $feeRepository,
        FeeConditionProcessorFactory $ruleFactory,
        FormData $formDataSerializer
    ) {
        $this->feeRepository = $feeRepository;
        $this->ruleFactory = $ruleFactory;
        $this->formDataSerializer = $formDataSerializer;

        return parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $feeId = null;
        $fee = $this->feeRepository->create();
        unset($data['entity_id']);

        if (array_key_exists('fee_id', $data)) {
            $feeId = $data['fee_id'];
            unset($data['fee_id']);

            if ($feeId) {
                $fee = $this->feeRepository->getById($feeId);
            }
        }

        try {
            $options = [];

            $unserializedOptions = $this->formDataSerializer->unserialize($data['serialized_options']);
            $data = array_merge($data, $unserializedOptions);

            if (isset($data['option'])) {
                $options = $data['option'];
                foreach ($options['value'] as $labels) {
                    if (empty($labels[0])) {
                        throw new LocalizedException(__('The value of Admin scope can\'t be empty.'));
                    }
                }
            }

            $this->prepareData($data);
            $fee->addData($data);
            $this->feeRepository->save($fee, $options);
            $feeId = $fee->getId();
            $returnToEdit = (bool)$this->getRequest()->getParam('back', false);

            $this->messageManager->addSuccessMessage(__('Extra fee was successfully saved'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $returnToEdit = true;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            $returnToEdit = true;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($feeId) {
                $resultRedirect->setPath('amasty_extrafee/*/edit', ['id' => $feeId, '_current' => true]);
            } else {
                $resultRedirect->setPath('amasty_extrafee/*/new', ['_current' => true]);
            }
        } else {
            $resultRedirect->setPath('amasty_extrafee/index');
        }

        return $resultRedirect;
    }

    /**
     * @param array $data
     */
    protected function prepareData(array &$data): void
    {
        if (array_key_exists('rule', $data)) {
            if (array_key_exists('conditions', $data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            if (array_key_exists('actions', $data['rule'])) {
                $data['actions'] = $data['rule']['actions'];
            }

            unset($data['rule']);

            $rule = $this->ruleFactory->create();
            $rule->loadPost($data);

            $data[FeeInterface::CONDITIONS_SERIALIZED] = $rule->getConditionsSerialized();
            $data[FeeInterface::PRODUCT_CONDITIONS_SERIALIZED] = $rule->getActionsSerialized();
            unset($data['conditions'], $data['actions'], $data['option']);
            if ((int)$data[FeeInterface::IS_PER_PRODUCT] === ApplyFeeFor::FOR_CART) {
                $data[FeeInterface::PRODUCT_CONDITIONS_SERIALIZED] = null;
            }
        }
    }
}
