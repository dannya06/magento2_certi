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
namespace Aheadworks\StoreCredit\Model;

use Aheadworks\StoreCredit\Api\Data\CustomerStoreCreditDetailsInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Aheadworks\StoreCredit\Model\CustomerStoreCreditDetails
 */
class CustomerStoreCreditDetails extends AbstractModel implements CustomerStoreCreditDetailsInterface
{
    /**
     *  {@inheritDoc}
     */
    public function getCustomerStoreCreditBalance()
    {
        return $this->getData(self::CUSTOMER_STORE_CREDIT_BALANCE);
    }

    /**
     *  {@inheritDoc}
     */
    public function setCustomerStoreCreditBalance($balance)
    {
        return $this->setData(self::CUSTOMER_STORE_CREDIT_BALANCE, $balance);
    }

    /**
     *  {@inheritDoc}
     */
    public function getCustomerStoreCreditBalanceCurrency()
    {
        return $this->getData(self::CUSTOMER_STORE_CREDIT_BALANCE_CURRENCY);
    }

    /**
     *  {@inheritDoc}
     */
    public function setCustomerStoreCreditBalanceCurrency($balance)
    {
        return $this->setData(self::CUSTOMER_STORE_CREDIT_BALANCE_CURRENCY, $balance);
    }

    /**
     *  {@inheritDoc}
     */
    public function getCustomerBalanceUpdateNotificationStatus()
    {
        return $this->getData(self::CUSTOMER_BALANCE_UPDATE_NOTIFICATION_STATUS);
    }

    /**
     *  {@inheritDoc}
     */
    public function setCustomerBalanceUpdateNotificationStatus($balanceUpdateNotificationStatus)
    {
        return $this->setData(self::CUSTOMER_BALANCE_UPDATE_NOTIFICATION_STATUS, $balanceUpdateNotificationStatus);
    }
}
