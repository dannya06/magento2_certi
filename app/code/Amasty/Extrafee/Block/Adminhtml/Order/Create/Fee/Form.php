<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Order\Create\Fee;

use Amasty\Extrafee\Model\FeesInformationManagement;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate;
use Magento\Sales\Model\AdminOrder\Create;

class Form extends AbstractCreate
{
    /** @var array */
    protected $rates;

    /**
     * @var FeesInformationManagement
     */
    private $feesInformationManagement;

    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        FeesInformationManagement $feesInformationManagement,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
        $this->feesInformationManagement = $feesInformationManagement;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_amasty_extrafee_form');
    }

    /**
     * @return array
     */
    public function getExtraFees()
    {
        if ($this->rates === null) {
            $this->rates = $this->feesInformationManagement->collectQuote($this->_orderCreate->getQuote());
        }

        return $this->rates;
    }

    /**
     * @param string $amount
     * @return string
     */
    public function getFormattedPrice($amount)
    {
        $amount = number_format($amount, 2);
        $pattern = $this->_storeManager->getStore($this->_orderCreate->getQuote()->getStoreId())
            ->getCurrentCurrency()->getOutputFormat();

        return str_replace('%s', $amount, $pattern);
    }
}
