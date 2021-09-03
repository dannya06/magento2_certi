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
namespace Aheadworks\SocialLogin\Model\Config\Source\LoginBlock;

/**
 * Class Group
 */
class Group implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Aheadworks\SocialLogin\Model\Config\LoginBlock
     */
    protected $loginBlockConfig;

    /**
     * @param \Aheadworks\SocialLogin\Model\Config\LoginBlock $loginBlockConfig
     */
    public function __construct(
        \Aheadworks\SocialLogin\Model\Config\LoginBlock $loginBlockConfig
    ) {
        $this->loginBlockConfig = $loginBlockConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $groups = $this->loginBlockConfig->getGroups();
        $options = [];
        if (is_array($groups)) {
            foreach ($groups as $group) {
                $options[] = [
                    'label' => $group,
                    'value' => $group
                ];
            }
        }
        return $options;
    }
}
