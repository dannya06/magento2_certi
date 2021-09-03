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
namespace Aheadworks\SocialLogin\Model\Account;

use Aheadworks\SocialLogin\Model\ProviderManagement;

/**
 * Class Type
 */
class Type implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var ProviderManagement
     */
    protected $providerManagement;

    /**
     * @param ProviderManagement $providerManagement
     */
    public function __construct(ProviderManagement $providerManagement)
    {
        $this->providerManagement = $providerManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->providerManagement->getList() as $providerFactory) {
            $options[] = [
                'label' => $providerFactory->getConfig()->getTitle(),
                'value' => $providerFactory->getConfig()->getCode()
            ];
        }
        return $options;
    }
}
