<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */

namespace Amasty\Promo\Block;

class Add extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Promo\Helper\Data
     */
    protected $promoHelper;
    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     * @param \Amasty\Promo\Helper\Data                        $promoHelper
     * @param \Magento\Framework\Url\Helper\Data               $urlHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Amasty\Promo\Helper\Data $promoHelper,
        \Magento\Framework\Url\Helper\Data $urlHelper
    ) {
        parent::__construct($context, $data);

        $this->promoHelper = $promoHelper;
        $this->urlHelper = $urlHelper;
    }

    public function hasItems()
    {
        return (bool)$this->promoHelper->getNewItems();
    }

    public function getMessage()
    {
        $message = $this->_scopeConfig->getValue(
            'ampromo/messages/add_message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $message;
    }

    public function isOpenAutomatically()
    {
        $auto = $this->_scopeConfig->isSetFlag(
            'ampromo/messages/auto_open_popup',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $auto && $this->hasItems();
    }
    
    public function getCurrentBase64Url()
    {
        return $this->urlHelper->getCurrentBase64Url();
    }
}
