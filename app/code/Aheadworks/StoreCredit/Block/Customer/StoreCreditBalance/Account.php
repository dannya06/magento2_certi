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
 * Class Aheadworks\StoreCredit\Block\Customer\StoreCreditBalance\Account
 */
class Account extends StoreCreditBalance
{
    /**
     * Retrieve customer transaction grid
     *
     * @return string
     */
    public function getTransactionHtml()
    {
        return $this->getChildHtml('aw_sc_transaction');
    }
}
