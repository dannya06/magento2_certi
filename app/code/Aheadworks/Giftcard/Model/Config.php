<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 *
 * @package Aheadworks\Giftcard\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GIFTCARD_EXPIRE_DAYS = 'aw_giftcard/general/expire_days';
    const XML_PATH_EMAIL_SENDER = 'aw_giftcard/email/sender';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Gift Card expire days
     *
     * @param int|null $websiteId
     * @return int
     */
    public function getGiftcardExpireDays($websiteId = null)
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_GIFTCARD_EXPIRE_DAYS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get email sender
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get email sender name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSenderName($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
