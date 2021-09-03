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
namespace Aheadworks\SocialLogin\Model\Config;

use Aheadworks\SocialLogin\Model\Serialize\Serializer;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Provider
 */
class Provider extends AbstractConfig implements ProviderInterface
{
    const XML_PATH_PROVIDER_IS_ENABLED = 'enabled';
    const XML_PATH_PROVIDER_TITLE = 'title';
    const XML_PATH_PROVIDER_SORT_ORDER = 'sort_order';

    /**
     * @var string
     */
    protected $code;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Serializer $serializer
     * @param string $code
     * @param string $pathPrefix
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Serializer $serializer,
        $code = '',
        $pathPrefix = ''
    ) {
        parent::__construct($scopeConfig, $serializer, $pathPrefix);
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::XML_PATH_PROVIDER_IS_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getValue(self::XML_PATH_PROVIDER_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return (int)$this->getValue(self::XML_PATH_PROVIDER_SORT_ORDER);
    }
}
