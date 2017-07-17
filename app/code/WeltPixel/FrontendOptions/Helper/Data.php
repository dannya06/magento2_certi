<?php

namespace WeltPixel\FrontendOptions\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_frontendOptions;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        
        $this->_frontendOptions = $this->scopeConfig->getValue('weltpixel_frontend_options', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMobileTreshold($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__m', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['breakpoints']['screen__m'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPageMainWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/page_main', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['page_main'];
        }
    }



    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPageMainPadding($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/page_main_padding', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['page_main_padding'];
        }
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFooterWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/footer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['footer'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getRowWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/row', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['row'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDefaultPageWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/default_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['default_page'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getCmsPageWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/cms_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['cms_page'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getCategoryPageWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/category_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['category_page'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductPageWidth($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/product_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_frontendOptions['section_width']['product_page'];
        }
    }
    
    /** Breakpoints are global **/
    /**
     * @return string
     */
    public function getBreakpointXXS() {
        return $this->_frontendOptions['breakpoints']['screen__xxs'];
    }

    /**
     * @return string
     */
    public function getBreakpointXS() {
        return $this->_frontendOptions['breakpoints']['screen__xs'];
    }

    /**
     * @return string
     */
    public function getBreakpointS() {
        return $this->_frontendOptions['breakpoints']['screen__s'];
    }

    /**
     * @return string
     */
    public function getBreakpointM() {
        return $this->_frontendOptions['breakpoints']['screen__m'];
    }

    /**
     * @return string
     */
    public function getBreakpointL() {
        return $this->_frontendOptions['breakpoints']['screen__l'];
    }

    /**
     * @return string
     */
    public function getBreakpointXL() {
        return $this->_frontendOptions['breakpoints']['screen__xl'];
    }

    /**
     * @return array
     */
    public function getAllBreakpoint() {
        return array(
            'xxs' => $this->getBreakpointXXS(),
            'xs' => $this->getBreakpointXS(),
            's' => $this->getBreakpointS(),
            'm' => $this->getBreakpointM(),
            'l' => $this->getBreakpointL(),
            'xl' => $this->getBreakpointXL(),
        );
    }

    public function getBreakPointsJson() {
        $brekpoints = [];
        $brekpoints['breakpoints'] = [];

        $minValue = 0;

        foreach ($this->getAllBreakpoint() as $key => $value) {
            $value = rtrim($value, 'px');
            $min = $minValue;
            $max = $value - 1;

//            if ($key == 'xxs') {
//                $min = 0;
//            }
            if ($key == 'xl') {
                $max = 10000;
            }

            $brekpoints['breakpoints'][$key] = [
                'enter' => $min,
                'exit'  => $max
            ];

            $minValue = $value;
        }

        return json_encode($brekpoints);
    }
}
