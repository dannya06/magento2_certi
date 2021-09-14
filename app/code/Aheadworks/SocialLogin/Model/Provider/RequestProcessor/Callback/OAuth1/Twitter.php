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
namespace Aheadworks\SocialLogin\Model\Provider\RequestProcessor\Callback\OAuth1;

use Aheadworks\SocialLogin\Model\Provider\RequestProcessor\Callback;
use Aheadworks\SocialLogin\Model\Provider\Service\ServiceInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Twitter callback request processor
 */
class Twitter extends Callback
{
    /**
     * {@inheritdoc}
     */
    protected function processRequest(ServiceInterface $service, RequestInterface $request)
    {
        /** @var \Aheadworks\SocialLogin\Model\Provider\Service\Twitter $service */

        $token = $service->getStorage()->retrieveAccessToken('Twitter');

        $service->requestAccessToken(
            $request->getParam('oauth_token'),
            $request->getParam('oauth_verifier'),
            $token->getRequestTokenSecret()
        );
    }
}
