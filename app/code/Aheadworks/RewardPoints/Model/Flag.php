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
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Model;

/**
 * Class Flag
 *
 * @package Aheadworks\RewardPoints\Model
 */
class Flag extends \Magento\Framework\Flag
{
    /**#@+
     * Constants for reward points flags
     */
    const AW_RP_EXPIRATION_CHECK_LAST_EXEC_TIME = 'aw_rp_expiration_check_last_exec_time';
    const AW_RP_EXPIRATION_REMINDER_LAST_EXEC_TIME = 'aw_rp_expiration_reminder_last_exec_time';
    const AW_RP_CUSTOMER_BIRTHDAY_LAST_EXEC_TIME = 'aw_rp_customer_birthday_last_exec_time';
    /**#@-*/

    /**
     * Setter for flag code
     * @codeCoverageIgnore
     *
     * @param string $code
     * @return $this
     */
    public function setRewardPointsFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
