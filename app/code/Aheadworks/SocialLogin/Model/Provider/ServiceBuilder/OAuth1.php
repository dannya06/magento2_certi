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
namespace Aheadworks\SocialLogin\Model\Provider\ServiceBuilder;

use Aheadworks\SocialLogin\Model\Provider\Service\ServiceInterface;
use Aheadworks\SocialLogin\Model\Provider\ServiceBuilder;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\OAuth1\Signature\Signature;

/**
 * OAuth1 Service builder
 */
class OAuth1 extends ServiceBuilder
{
    /**
     * @return ServiceInterface
     */
    public function build()
    {
        /** @var ServiceInterface $service */
        $service = $this->objectManager->create($this->service, [
            'credentials'   => $this->credentials,
            'httpClient'    => new CurlClient(),
            'storage'       => $this->storage,
            'signature'     => new Signature($this->credentials),
            'baseApiUri'    => $this->config->getBaseUri()
        ]);
        return $service;
    }
}
