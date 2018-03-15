<?php
namespace WeltPixel\Command\Block\Cache;

class Additional extends \Magento\Backend\Block\Template
{

    /**
     * Additional constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCssGenerationUrl()
    {
        return $this->getUrl('weltpixelcommand/cache/generateCss');
    }

    /**
     * @return array
     */
    public function getStoreViews() {
        $storeCodes = [];
        $stores = $this->_storeManager->getStores();
        foreach ($stores as $store) {
            $storeCodes[] = [
                'value' => $store->getCode(),
                'label' => $store->getName(),
            ];
        }


        if (count($storeCodes) > 1) {
            array_unshift($storeCodes, [
                'value' => 0,
                'label' => __('Please select store view')
            ], [
                'value' => '-',
                'label' => __('ALL STORE VIEWS')
            ]);
        }

        return $storeCodes;
    }
}
