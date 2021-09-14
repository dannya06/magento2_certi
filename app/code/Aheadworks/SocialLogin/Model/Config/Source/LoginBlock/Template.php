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

use Aheadworks\SocialLogin\Model\LoginBlock\Template\Provider;

/**
 * Class Template
 */
class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @param Provider $provider
     */
    public function __construct(
        Provider $provider
    ) {
        $this->provider = $provider;
    }
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $templatesData = $this->provider->getTemplatesData();
        $options = [];
        foreach ($templatesData as $templateId => $templateData) {
            $options[$templateId] = isset($templateData['title']) ? $templateData['title'] : $templateId;
        }
        return $options;
    }
}
