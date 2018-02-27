<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email;

use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\EmailManagementInterface;
use Aheadworks\Followupemail2\Api\Data\StatisticsInterface;
use Aheadworks\Followupemail2\Model\Source\Email\Status as EmailStatusSource;
use Aheadworks\Followupemail2\Ui\DataProvider\Event\ManageFormProcessor;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class ChangeStatus
 * @package Aheadworks\Followupemail2\Controller\Adminhtml\Event\Email
 */
class ChangeStatus extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Followupemail2::campaigns_actions';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var EmailManagementInterface
     */
    private $emailManagement;

    /**
     * @var EmailStatusSource
     */
    private $emailStatusSource;

    /**
     * @var ManageFormProcessor
     */
    private $manageFormProcessor;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param EmailManagementInterface $emailManagement
     * @param EmailStatusSource $emailStatusSource
     * @param ManageFormProcessor $manageFormProcessor
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        EmailManagementInterface $emailManagement,
        EmailStatusSource $emailStatusSource,
        ManageFormProcessor $manageFormProcessor,
        DataObjectProcessor $dataObjectProcessor
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->emailManagement = $emailManagement;
        $this->emailStatusSource = $emailStatusSource;
        $this->manageFormProcessor = $manageFormProcessor;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var EmailInterface $email */
                $email = $this->emailManagement->changeStatus($id);
                $emailData = $this->dataObjectProcessor->buildOutputDataArray(
                    $email,
                    EmailInterface::class
                );

                /** @var StatisticsInterface $emailStatistics */
                $emailStatistics = $this->emailManagement->getStatistics($email);
                $emailData['when'] = $this->manageFormProcessor->getWhen($email);
                $emailData['sent'] = $emailStatistics->getSent();
                $emailData['opened'] = $emailStatistics->getOpened();
                $emailData['clicks'] = $emailStatistics->getClicked();
                $emailData['open_rate'] = $emailStatistics->getOpenRate();
                $emailData['click_rate'] = $emailStatistics->getClickRate();
                $emailData['status'] = $this->emailStatusSource->getOptionByValue($email->getStatus());
                $emailData['is_email_disabled'] = ($email->getStatus() == EmailInterface::STATUS_DISABLED);

                $result = [
                    'error'     => false,
                    'message'   => __('Success.'),
                    'email'     => $emailData
                ];
            } catch (\Exception $e) {
                $result = [
                    'error'     => true,
                    'message'   => __($e->getMessage())
                ];
            }
        } else {
            $result = [
                'error'     => true,
                'message'   => __('Email Id is not specified!')
            ];
        }
        return $resultJson->setData($result);
    }
}
