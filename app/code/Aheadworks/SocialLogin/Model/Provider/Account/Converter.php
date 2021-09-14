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
 * @package    SocialLogin
 * @version    1.6.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\SocialLogin\Model\Provider\Account;

use Aheadworks\SocialLogin\Model\Provider\AccountInterface as ProviderAccountInterface;
use Aheadworks\SocialLogin\Api\Data\AccountInterface;

/**
 * Class Converter
 */
class Converter implements ConverterInterface
{
    /**
     * @var \Aheadworks\SocialLogin\Model\AccountFactory
     */
    protected $accountFactory;

    /**
     * @param \Aheadworks\SocialLogin\Model\AccountFactory $accountFactory
     */
    public function __construct(
        \Aheadworks\SocialLogin\Model\AccountFactory $accountFactory
    ) {
        $this->accountFactory = $accountFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(ProviderAccountInterface $providerAccount)
    {
        $account = $this->initAccount();

        $account->setType($providerAccount->getType())
            ->setFirstName($providerAccount->getFirstName())
            ->setLastName($providerAccount->getLastName())
            ->setEmail($providerAccount->getEmail())
            ->setSocialId($providerAccount->getSocialId());

        //@TODO image upload
        $account->setImagePath($providerAccount->getImageUrl());

        return $account;
    }

    /**
     * Init account model
     *
     * @return AccountInterface
     */
    protected function initAccount()
    {
        return $this->accountFactory->create();
    }
}
