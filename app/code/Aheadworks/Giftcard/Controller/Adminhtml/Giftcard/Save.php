<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Giftcard;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\Data\GiftcardInterfaceFactory;
use Aheadworks\Giftcard\Api\GiftcardManagementInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\Config;
use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Aheadworks\Giftcard\Model\Source\Giftcard\EmailTemplate;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_codes';

    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var GiftcardManagementInterface
     */
    private $giftcardManagement;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var GiftcardInterfaceFactory
     */
    private $giftcardDataFactory;

    /**
     * @param Context $context
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param GiftcardManagementInterface $giftcardManagement
     * @param Config $config
     * @param DateTime $dateTime
     * @param TimezoneInterface $localeDate
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param GiftcardInterfaceFactory $giftcardDataFactory
     */
    public function __construct(
        Context $context,
        GiftcardRepositoryInterface $giftcardRepository,
        GiftcardManagementInterface $giftcardManagement,
        Config $config,
        DateTime $dateTime,
        TimezoneInterface $localeDate,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        GiftcardInterfaceFactory $giftcardDataFactory
    ) {
        parent::__construct($context);
        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardManagement = $giftcardManagement;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->giftcardDataFactory = $giftcardDataFactory;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $data = $this->prepareData($data);
            try {
                $giftcard = $this->performSave($data);

                $this->dataPersistor->clear('aw_giftcard_giftcard');
                $this->messageManager->addSuccessMessage(__('Gift Card Code was successfully saved'));
                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $giftcard->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Gift Card code')
                );
            }
            $this->dataPersistor->set('aw_giftcard_giftcard', $data);
            $id = isset($data['id']) ? $data['id'] : false;
            if ($id) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare data before save
     *
     * @param [] $data
     * @return []
     */
    private function prepareData($data)
    {
        if ($data['id'] && isset($data['expire_at']) && $data['expire_at']) {
            $data['expire_at'] = $this->dateTime->gmtDate(
                StdlibDateTime::DATETIME_PHP_FORMAT,
                $data['expire_at']
            );
        }
        if (!$data['id']) {
            $expireAfter = null;
            if (isset($data['use_default']) && isset($data['use_default']['expire_after'])
                && (bool)$data['use_default']['expire_after']
            ) {
                $expireAfter = $this->config->getGiftcardExpireDays();
            }
            if (null === $expireAfter && isset($data['expire_after'])) {
                $expireAfter = $data['expire_after'];
            }
            if ($expireAfter) {
                $data['expire_at'] = $this->localeDate
                    ->date('+' . $expireAfter . 'days', null, false)
                    ->format(StdlibDateTime::DATETIME_PHP_FORMAT);
            }
        }
        if (isset($data['delivery_date']) && $data['delivery_date']) {
            $deliveryDate = new \DateTime($data['delivery_date'], new \DateTimeZone($data['delivery_date_timezone']));
            $deliveryDate->setTimezone(new \DateTimeZone('UTC'));
            $data['delivery_date'] = $deliveryDate->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        } else {
            $data['delivery_date_timezone'] = null;
        }
        return $data;
    }

    /**
     * Perform save
     *
     * @param [] $data
     * @return GiftcardInterface
     */
    private function performSave($data)
    {
        $saveAction = $this->getRequest()->getParam('action');
        $id = isset($data['id']) ? $data['id'] : false;
        $dataObject = $id
            ? $this->giftcardRepository->get($id)
            : $this->giftcardDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $dataObject,
            $data,
            GiftcardInterface::class
        );
        if (!$dataObject->getId()) {
            $dataObject->setId(null);
        }
        $giftcard = $this->giftcardRepository->save($dataObject);
        if ($saveAction == 'save_and_send' && $giftcard->getEmailTemplate() != EmailTemplate::DO_NOT_SEND) {
            $giftcards = $this->giftcardManagement->sendGiftcardByCode($giftcard->getCode(), false);
            $giftcard = count($giftcards) ? array_shift($giftcards) : null;
            if ($giftcard && $giftcard->getEmailSent() == EmailStatus::SENT) {
                $this->messageManager->addSuccessMessage(__('Email was successfully sent'));
            } else {
                $this->messageManager->addErrorMessage(__('Could not send email'));
            }
        }
        return $giftcard;
    }
}
