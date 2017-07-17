<?php
namespace WeltPixel\FrontendOptions\Plugin;

class PageConfigStructure {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_helper;

    /**
     * Head constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\FrontendOptions\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\FrontendOptions\Helper\Data $helper
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
    }


    /**
     * Modify the hardcoded breakpoint for styles-l.css
     * @param \Magento\Framework\View\Page\Config\Structure $subject
     * @param string $name
     * @param array $attributes
     * @return $this
     */
    public function beforeAddAssets(\Magento\Framework\View\Page\Config\Structure $subject,
        $name, $attributes)
    {
        if ($name == 'css/styles-l.css') {
            $attributes['media'] = 'screen and (min-width: ' . $this->_helper->getMobileTreshold() . ')';
        }

        return [$name, $attributes];
    }
}