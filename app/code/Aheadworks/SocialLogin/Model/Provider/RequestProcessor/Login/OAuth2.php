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
namespace Aheadworks\SocialLogin\Model\Provider\RequestProcessor\Login;

use Aheadworks\SocialLogin\Model\Provider\RequestProcessor\Login;
use Aheadworks\SocialLogin\Model\Provider\Service\ServiceInterface;

/**
 * Class OAuth2 login processor
 */
class OAuth2 extends Login
{
    /**
     * {@inheritdoc}
     */
    public function process(ServiceInterface $service, \Magento\Framework\App\RequestInterface $request)
    {
        $authUrl = $service->getAuthorizationUri();
        return $this->buildRedirect($authUrl);
    }
}
