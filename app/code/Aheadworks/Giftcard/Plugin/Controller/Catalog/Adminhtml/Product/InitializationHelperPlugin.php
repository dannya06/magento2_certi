<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Controller\Catalog\Adminhtml\Product;

use Aheadworks\Giftcard\Api\Data\AmountInterface;
use Aheadworks\Giftcard\Api\Data\AmountInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\TemplateInterface;
use Aheadworks\Giftcard\Api\Data\TemplateInterfaceFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;
use Magento\Catalog\Model\Product;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class InitializationHelperPlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Controller\Catalog\Adminhtml\Product
 */
class InitializationHelperPlugin
{
    /**
     * @var AmountInterfaceFactory
     */
    private $amountFactory;

    /**
     * @var TemplateInterfaceFactory
     */
    private $templateFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param AmountInterfaceFactory $amountFactory
     * @param TemplateInterfaceFactory $templateFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        AmountInterfaceFactory $amountFactory,
        TemplateInterfaceFactory $templateFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->amountFactory = $amountFactory;
        $this->templateFactory = $templateFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Add Gift card extension attributes after initialize product
     *
     * @param InitializationHelper $subject
     * @param Product              $product
     *
     * @return Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterInitialize(InitializationHelper $subject, Product $product)
    {
        if ($product->getTypeId() != ProductGiftcard::TYPE_CODE) {
            return $product;
        }

        $extension = $product->getExtensionAttributes();
        $extension->setAwGiftcardAmounts($this->getAmounts($product));
        $extension->setAwGiftcardTemplates($this->getTemplates($product));
        $product->setExtensionAttributes($extension);
        return $product;
    }

    /**
     * Retrieve Gift Card amounts
     *
     * @param Product $product
     * @return AmountInterface[]
     */
    private function getAmounts($product)
    {
        $amountsData = $product->getData(ProductAttributeInterface::CODE_AW_GC_AMOUNTS) ;
        $amounts = [];
        if (!is_array($amountsData)) {
            return $amounts;
        }
        foreach ($amountsData as $amountData) {
            if (empty($amountData['delete'])) {
                if (isset($amountData['price'])) {
                    $amountData['value'] = $amountData['price'];
                }
                $amountDataObject = $this->amountFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $amountDataObject,
                    $amountData,
                    AmountInterface::class
                );
                $amounts[] = $amountDataObject;
            }
        }
        return $amounts;
    }

    /**
     * Retrieve Gift Card templates
     *
     * @param Product $product
     * @return TemplateInterface[]
     */
    private function getTemplates($product)
    {
        $templatesData = $product->getData(ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES);
        $templates = [];
        if (!is_array($templatesData)) {
            return $templates;
        }
        foreach ($templatesData as $templateData) {
            if (empty($templateData['delete'])) {
                if (isset($templateData['image'][0])) {
                    $templateData['image'] = $templateData['image'][0]['file'];
                }
                if (isset($templateData['template'])) {
                    $templateData['value'] = $templateData['template'];
                }
                $templateDataObject = $this->templateFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $templateDataObject,
                    $templateData,
                    TemplateInterface::class
                );
                $templates[] = $templateDataObject;
            }
        }
        return $templates;
    }
}
