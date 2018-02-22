<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Ui\DataProvider\Event;

use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EmailContentInterface;
use Aheadworks\Followupemail2\Api\EmailRepositoryInterface;
use Aheadworks\Followupemail2\Api\EmailManagementInterface;
use Aheadworks\Followupemail2\Model\Source\VariablesFactory as FueVariablesSourceFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Email\Model\Source\Variables as EmailVariablesSource;
use Magento\Variable\Model\VariableFactory as EmailVariablesFactory;

/**
 * Class FormDataProvider
 * @package Aheadworks\Followupemail2\Ui\DataProvider\Event
 * @codeCoverageIgnore
 */
class EmailFormDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var EmailRepositoryInterface
     */
    private $emailRepository;

    /**
     * @var EmailManagementInterface
     */
    private $emailManagement;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var EmailVariablesSource
     */
    private $emailVariablesSource;

    /**
     * @var EmailVariablesFactory
     */
    private $emailVariablesFactory;

    /**
     * @var FueVariablesSourceFactory
     */
    private $fue2VariablesSourceFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param EventRepositoryInterface $eventRepository
     * @param EmailRepositoryInterface $emailRepository
     * @param EmailManagementInterface $emailManagement
     * @param RequestInterface $request
     * @param DataObjectProcessor $dataObjectProcessor
     * @param EmailVariablesSource $emailVariablesSource
     * @param EmailVariablesFactory $emailVariablesFactory
     * @param FueVariablesSourceFactory $fue2VariablesSourceFactory
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EventRepositoryInterface $eventRepository,
        EmailRepositoryInterface $emailRepository,
        EmailManagementInterface $emailManagement,
        RequestInterface $request,
        DataObjectProcessor $dataObjectProcessor,
        EmailVariablesSource $emailVariablesSource,
        EmailVariablesFactory $emailVariablesFactory,
        FueVariablesSourceFactory $fue2VariablesSourceFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->eventRepository = $eventRepository;
        $this->emailRepository = $emailRepository;
        $this->emailManagement = $emailManagement;
        $this->request = $request;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->emailVariablesSource = $emailVariablesSource;
        $this->emailVariablesFactory = $emailVariablesFactory;
        $this->fue2VariablesSourceFactory = $fue2VariablesSourceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $id = $this->request->getParam($this->getRequestFieldName());
        $duplicate = $this->request->getParam('duplicate');
        $data = [];
        if ($id) {
            try {
                /** @var EmailInterface $emailDataObject */
                $emailDataObject = $this->emailRepository->get($id);

                $formData = $this->dataObjectProcessor->buildOutputDataArray(
                    $emailDataObject,
                    EmailInterface::class
                );

                $formData = $this->getPreparedData($formData);
                $formData = $this->convertToString(
                    $formData,
                    [
                        EmailInterface::STATUS,
                        EmailInterface::AB_TESTING_MODE,
                        EmailInterface::PRIMARY_EMAIL_CONTENT,
                    ]
                );

                $formData['variables'] = $this->getVariables($emailDataObject->getEventId());

                if ($duplicate) {
                    unset($formData[EmailInterface::ID]);
                    if (isset($formData[EmailInterface::CONTENT])) {
                        foreach ($formData[EmailInterface::CONTENT] as &$content) {
                            unset($content[EmailContentInterface::ID]);
                            unset($content[EmailContentInterface::EMAIL_ID]);
                        }
                    }
                    $formData[EmailInterface::STATUS] = EmailInterface::STATUS_DISABLED;
                    $formData[EmailInterface::NAME] .= ' #1';
                    $formData[EmailInterface::POSITION] = $this->emailManagement->getNewEmailPosition(
                        $emailDataObject->getEventId()
                    );
                    $data[$id] = $formData;
                } else {
                    $data[$id] = $formData;
                }
            } catch (NoSuchEntityException $e) {
            }
        } else {
            if (isset($this->data['config']['params'])) {
                $params = $this->data['config']['params'];
                $paramValues = [];
                foreach ($params as $param) {
                    $paramValue = $this->request->getParam($param);
                    if ($paramValue) {
                        $paramValues[$param] = $paramValue;
                        if ($param == EmailInterface::EVENT_ID) {
                            $paramValues['variables'] = $this->getVariables($paramValue);
                        }
                    }
                }

                $data[null] = $paramValues;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }

    /**
     * Convert selected fields to string
     *
     * @param [] $data
     * @param string[] $fields
     * @return []
     */
    private function convertToString($data, $fields)
    {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                if (is_array($data[$field])) {
                    foreach ($data[$field] as $key => $value) {
                        if ($value === false) {
                            $data[$field][$key] = '0';
                        } else {
                            $data[$field][$key] = (string)$value;
                        }
                    }
                } else {
                    $data[$field] = (string)$data[$field];
                }
            }
        }
        return $data;
    }

    /**
     * Get prepared form data
     *
     * @param array $formData
     * @return array
     */
    private function getPreparedData($formData)
    {
        foreach ($formData[EmailContentInterface::CONTENT] as &$content) {
            if (isset($content[EmailContentInterface::SENDER_NAME])
                && $content[EmailContentInterface::SENDER_NAME]
            ) {
                $content['use_config'][EmailContentInterface::SENDER_NAME] = 0;
            }

            if (isset($content[EmailContentInterface::SENDER_EMAIL])
                && $content[EmailContentInterface::SENDER_EMAIL]
            ) {
                $content['use_config'][EmailContentInterface::SENDER_EMAIL] = 0;
            }

            if (isset($content[EmailContentInterface::SENDER_EMAIL])
                && $content[EmailContentInterface::SENDER_EMAIL]
            ) {
                $content['use_config'][EmailContentInterface::SENDER_EMAIL] = 0;
            }

            if (isset($content[EmailContentInterface::HEADER_TEMPLATE])
                && $content[EmailContentInterface::HEADER_TEMPLATE]
            ) {
                $content['use_config'][EmailContentInterface::HEADER_TEMPLATE] = 0;
            }

            if (isset($content[EmailContentInterface::FOOTER_TEMPLATE])
                && $content[EmailContentInterface::FOOTER_TEMPLATE]
            ) {
                $content['use_config'][EmailContentInterface::FOOTER_TEMPLATE] = 0;
            }
        }

        return $formData;
    }

    /**
     * Is currently edited email first in the chain
     *
     * @return bool
     */
    public function isCurrentEmailFirst()
    {
        $id = $this->request->getParam($this->getRequestFieldName());
        if ($id) {
            try {
                /** @var EmailInterface $emailDataObject */
                $emailDataObject = $this->emailRepository->get($id);
                return $this->emailManagement->isFirst($emailDataObject->getId(), $emailDataObject->getEventId());
            } catch (NoSuchEntityException $e) {
            }
        } else {
            $eventId = $this->request->getParam(EmailInterface::EVENT_ID);
            try {
                /** @var EventInterface $emailDataObject */
                $event = $this->eventRepository->get($eventId);
                return $this->emailManagement->isFirst(null, $event->getId());
            } catch (NoSuchEntityException $e) {
            }
        }
        return false;
    }

    /**
     * Retrieve variables to insert into email
     *
     * @param int $eventId
     * @return array
     */
    public function getVariables($eventId)
    {
        $variables = [];
        $variables[] = $this->emailVariablesSource->toOptionArray(true);
        $customVariables = $this->emailVariablesFactory->create()->getVariablesOptionArray(true);
        if ($customVariables) {
            $variables[] = $customVariables;
        }

        try {
            /** @var EventInterface $event */
            $event = $this->eventRepository->get($eventId);
            $fue2VariablesSource = $this->fue2VariablesSourceFactory->create(
                ['eventType' => $event->getEventType()]
            );
            $variables[] = $fue2VariablesSource->toOptionArray(true);
        } catch (NoSuchEntityException $e) {
        }

        return $variables;
    }
}
