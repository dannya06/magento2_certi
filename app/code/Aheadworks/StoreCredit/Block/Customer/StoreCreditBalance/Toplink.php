<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Block\Customer\StoreCreditBalance;

use Aheadworks\StoreCredit\Block\Customer\StoreCreditBalance;

/**
 * Class Aheadworks\StoreCredit\Block\Customer\StoreCreditBalance\Toplink
 */
class Toplink extends StoreCreditBalance
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_StoreCredit::customer/toplinks/balance.phtml';

    /**
     * Is ajax request or not
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isAjax();
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Can show block
     *
     * @return bool
     */
    public function canShow()
    {
        if ($this->config->isStoreCreditBalanceTopLinkAtFrontend()
            && (!$this->config->isHideIfStoreCreditBalanceEmpty()
                || ($this->config->isHideIfStoreCreditBalanceEmpty() &&
                    (float)$this->getCustomerStoreCreditBalance() > 0))
        ) {
            return true;
        }
        return false;
    }
}
