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

use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class KeyEncryptor
 *
 * @package Aheadworks\RewardPoints\Model
 */
class KeyEncryptor
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * Encrypt external key
     *
     * @param string $customerEmail
     * @param int $customerId
     * @param int $websiteId
     * @return string
     * phpcs:disable Magento2.Functions.DiscouragedFunction
     */
    public function encrypt($customerEmail, $customerId, $websiteId)
    {
        return base64_encode($this->encryptor->encrypt($customerEmail . ',' . $customerId . ',' . $websiteId));
    }

    /**
     * Decrypt external key
     *
     * @param string $key
     * @return string
     * phpcs:disable Magento2.Functions.DiscouragedFunction
     */
    public function decrypt($key)
    {
        list($email, $customerId, $websiteId) = explode(',', $this->encryptor->decrypt(base64_decode($key)));
        return ['email' => $email, 'customer_id' => $customerId, 'website_id' => $websiteId];
    }
}
