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
namespace Aheadworks\SocialLogin\Model\Provider\Customer;

use Aheadworks\SocialLogin\Exception\CustomerConvertException;
use Aheadworks\SocialLogin\Model\Provider\AccountInterface as ProviderAccountInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class Converter
 */
class Converter implements ConverterInterface
{
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param ValidatorInterface $validator
     */
    public function __construct(
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        ValidatorInterface $validator
    ) {
        $this->customerFactory = $customerFactory;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(ProviderAccountInterface $providerAccount)
    {
        $customer = $this->initCustomer()
            ->setFirstname($providerAccount->getFirstName())
            ->setLastname($providerAccount->getLastName())
            ->setEmail($providerAccount->getEmail());

        if (!$this->validator->isValid($customer)) {
            throw new CustomerConvertException(
                __('Invalid customer'),
                $this->validator->validate($customer)
            );
        }

        return $customer;
    }

    /**
     * Init customer model
     *
     * @return CustomerInterface
     */
    protected function initCustomer()
    {
        return $this->customerFactory->create();
    }
}
