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
namespace Aheadworks\SocialLogin\Model\Provider\Service\Config;

/**
 * Class Base
 */
class Base implements ConfigInterface
{
    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @param array $scopes
     * @param string|null $baseUri
     */
    public function __construct(
        $scopes = [],
        $baseUri = null
    ) {
        $this->scopes = $scopes;
        $this->baseUri = $baseUri;
    }

    /**
     * Get access scopes
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Get base uri
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }
}
