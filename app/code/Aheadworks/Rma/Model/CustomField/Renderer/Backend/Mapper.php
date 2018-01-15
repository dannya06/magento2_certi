<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\CustomField\Renderer\Backend;

use Aheadworks\Rma\Api\Data\CustomFieldInterface;
use Aheadworks\Rma\Model\CustomField\AvailabilityChecker;
use Aheadworks\Rma\Model\Source\CustomField\EditAt;
use Aheadworks\Rma\Model\Source\CustomField\Type;

/**
 * Class Mapper
 *
 * @package Aheadworks\Rma\Model\CustomField\Renderer\Backend
 */
class Mapper
{
    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @var array
     */
    private $formElementMap = [
        Type::TEXT => 'input'
    ];

    /**
     * @var array
     */
    private $dataTypeElementMap = [
        Type::TEXT => 'text',
        Type::TEXT_AREA => 'text',
        Type::SELECT => 'number',
        Type::MULTI_SELECT => 'number'
    ];

    /**
     * @var array
     */
    private $propertiesMap = [
        'required' => 'isRequired',
        'label' => 'getStorefrontLabel'
    ];

    /**
     * @param AvailabilityChecker $availabilityChecker
     */
    public function __construct(
        AvailabilityChecker $availabilityChecker
    ) {
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * Map custom field attribute
     *
     * @param CustomFieldInterface $customField
     * @param int $requestStatus
     * @return array
     */
    public function map($customField, $requestStatus)
    {
        $result = [];
        foreach ($this->propertiesMap as $sourceFieldName => $methodName) {
            $result[$sourceFieldName] = $customField->$methodName();
        }
        if (count($customField->getOptions())) {
            $result = array_merge($result, $this->prepareOptions($customField, $requestStatus));
        }

        $type = $customField->getType();
        $result['dataType'] = isset($this->dataTypeElementMap[$type])
                ? $this->dataTypeElementMap[$type]
                : $type;
        $result['formElement'] = isset($this->formElementMap[$type])
            ? $this->formElementMap[$type]
            : $type;
        $result['disabled'] = !$this->isEditable($customField, $requestStatus);
        if (in_array($type, [Type::SELECT, Type::MULTI_SELECT])) {
            $result['caption'] = $result['disabled'] ? __('Please select') : false;
        }
        $result = array_merge($result, $this->getValidationRules($customField));

        return $result;
    }

    /**
     * Get validation rules data
     *
     * @param CustomFieldInterface $customField
     * @return array
     */
    private function getValidationRules($customField)
    {
        $rules = [];
        if ($customField->isRequired()) {
            $rules['validation']['required-entry'] = true;
            $rules['columnsHeaderClasses']['required'] = true;
        }

        return $rules;
    }

    /**
     * Prepare options data
     *
     * @param CustomFieldInterface $customField
     * @param int $requestStatus
     * @return array
     */
    private function prepareOptions($customField, $requestStatus)
    {
        $optionsData = [];
        $defaultOptionsData = [];
        if (in_array($requestStatus, [EditAt::NEW_REQUEST_PAGE]) && $customField->getType() != Type::MULTI_SELECT) {
            $optionsData[] = ['value' => '', 'label' => __('Please select')];
        }
        foreach ($customField->getOptions() as $option) {
            if (!$option->getEnabled()) {
                continue;
            }
            $optionsData[] = [
                'value' => $option->getId(),
                'label' => $option->getStorefrontLabel()
            ];
            if ($option->isDefault()) {
                $defaultOptionsData[] = $option->getId();
            }
        }

        return ['options' => $optionsData, 'default_options' => $defaultOptionsData];
    }

    /**
     * Check is editable
     *
     * @param CustomFieldInterface $customField
     * @param int $requestStatus
     * @return bool
     */
    private function isEditable($customField, $requestStatus)
    {
        return $this->availabilityChecker->canEditableAdminByStatus($customField->getId(), $requestStatus);
    }
}
