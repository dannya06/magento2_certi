<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Fee\Edit\Tab\Option;

use Amasty\Extrafee\Api\FeeRepositoryInterface;
use Amasty\Extrafee\Model\Fee;
use Amasty\Extrafee\Model\Fee\Source\PriceType;
use Amasty\Extrafee\Model\StoresSorter;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Field extends Widget implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'fee/options.phtml';

    /** @var PriceType  */
    protected $priceType;

    /**
     * @var FeeRepositoryInterface
     */
    private $feeRepository;

    /**
     * @var StoresSorter
     */
    private $storesSorter;

    public function __construct(
        Context $context,
        PriceType $priceType,
        FeeRepositoryInterface $feeRepository,
        StoresSorter $storesSorter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->priceType = $priceType;
        $this->feeRepository = $feeRepository;
        $this->storesSorter = $storesSorter;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * @return array
     */
    public function getStoresSortedBySortOrder()
    {
        return $this->storesSorter->getStoresSortedBySortOrder($this->getStores());
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $this->setData('stores', $this->_storeManager->getStores(true));
        }
        return $this->_getData('stores');
    }

    /**
     * @return array
     */
    public function getOptionValues()
    {
        $values = [];
        if ($feeId = $this->getRequest()->getParam('id')) {
            $model = $this->feeRepository->getById($feeId);
            foreach ($model->getOptions() as $option) {
                $storesData = [];
                foreach ($this->getStores() as $store) {
                    $storesData['store' . $store->getId()] = array_key_exists('options', $option)
                    && array_key_exists($store->getId(), $option['options'])
                        ? $option['options'][$store->getId()]
                        : '';
                }

                $storesData = array_merge_recursive($storesData, [
                    'checked' =>
                        array_key_exists('default', $option) && $option['default'] ? 'checked="checked"' : '',
                    'price' => array_key_exists('price', $option) && $option['price'] ? $option['price'] : '',
                    'price_type' => array_key_exists('price_type', $option) && $option['price_type']
                        ? $option['price_type']
                        : Fee::PRICE_TYPE_FIXED,
                    'intype' => 'radio',
                    'id' => $option['entity_id'],
                    'sort_order' => $option['order']
                ]);

                $values[] = $storesData;
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getPriceTypes()
    {
        return $this->priceType->toOptionArray();
    }
}
