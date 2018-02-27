<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Ui\Component\Form\Request;

use Aheadworks\Rma\Api\CustomFieldRepositoryInterface;
use Aheadworks\Rma\Api\Data\CustomFieldInterface;
use Aheadworks\Rma\Api\Data\RequestInterface;
use Aheadworks\Rma\Api\RequestRepositoryInterface;
use Aheadworks\Rma\Model\CustomField\Renderer\Backend\Mapper;
use Aheadworks\Rma\Model\Source\CustomField\EditAt;
use Aheadworks\Rma\Model\Source\CustomField\Refers;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Container;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;

/**
 * Class CustomFields
 *
 * @package Aheadworks\Rma\Ui\Component\Form\Request
 */
class CustomFields extends Container
{
    /**
     * @var UiComponentFactory
     */
    private $uiComponentFactory;

    /**
     * @var CustomFieldRepositoryInterface
     */
    private $customFieldRepository;

    /**
     * @var RequestRepositoryInterface
     */
    private $requestRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomFieldRepositoryInterface $customFieldRepository
     * @param RequestRepositoryInterface $requestRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param Mapper $mapper
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomFieldRepositoryInterface $customFieldRepository,
        RequestRepositoryInterface $requestRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        Mapper $mapper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->uiComponentFactory = $uiComponentFactory;
        $this->customFieldRepository = $customFieldRepository;
        $this->requestRepository = $requestRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $status = $this->getRequestStatus();
        $refersTo = $this->getData('config/refersTo') ? : Refers::REQUEST;
        foreach ($this->getCustomFields($refersTo) as $customField) {
            $config = $this->mapper->map($customField, $status);
            $this->createComponent(
                $this->getCustomFieldName($customField),
                Field::NAME,
                $config
            );
        }

        parent::prepare();
    }

    /**
     * Retrieve custom field name
     *
     * @param CustomFieldInterface $customField
     * @return string
     */
    private function getCustomFieldName($customField)
    {
        return 'custom_fields' . '.' . $customField->getId();
    }

    /**
     * Retrieve custom fields
     *
     * @param string $refersTo
     * @return CustomFieldInterface[]
     */
    private function getCustomFields($refersTo)
    {
        $this->searchCriteriaBuilder
            ->addFilter(CustomFieldInterface::REFERS, $refersTo)
            ->addFilter(CustomFieldInterface::OPTIONS, 'enabled')
            ->addFilter(CustomFieldInterface::WEBSITE_IDS, $this->storeManager->getWebsite()->getId());

        return $this->customFieldRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();
    }

    /**
     * Create component
     *
     * @param string $fieldName
     * @param string $type
     * @param array $config
     * @return $this
     */
    private function createComponent($fieldName, $type, $config)
    {
        $component = $this->uiComponentFactory->create(
            $fieldName,
            $type,
            ['context' => $this->getContext()]
        );
        $component->setData('config', $config);
        $component->prepare();
        $this->addComponent($fieldName, $component);

        return $this;
    }

    /**
     * Retrieve request status
     *
     * @return int
     */
    private function getRequestStatus()
    {
        $id = $this->getContext()->getRequestParam(
            $this->getContext()->getDataProvider()->getRequestFieldName()
        );

        return !empty($id)
            ? $this->requestRepository->get($id)->getStatusId()
            : EditAt::NEW_REQUEST_PAGE;
    }
}
