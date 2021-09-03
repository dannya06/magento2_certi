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
namespace Aheadworks\SocialLogin\Test\Page\SocialProvider;

use Magento\Mtf\Page\FrontendPage;

/**
 * Class LoginPagePool
 */
class LoginPagePool
{
    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @param array $pages
     */
    public function __construct(
        array $pages = []
    ) {
        $this->pages = $pages;
    }

    /**
     * Get page by provider
     *
     * @param $providerName
     * @return FrontendPage
     */
    public function getPage($providerName)
    {
        if (!isset($this->pages[$providerName])) {
            return null;
        }
        return $this->pages[$providerName];
    }
}
