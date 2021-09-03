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
namespace Aheadworks\SocialLogin\Model\Provider\RequestProcessor;

use Aheadworks\SocialLogin\Model\Provider\AccountInterface;
use Aheadworks\SocialLogin\Model\Provider\Service\ServiceInterface;

/**
 * Interface CallbackInterface
 */
interface CallbackInterface
{
    /**
     * @param ServiceInterface $service
     * @param \Magento\Framework\App\RequestInterface $request
     * @return AccountInterface
     */
    public function process(ServiceInterface $service, \Magento\Framework\App\RequestInterface $request);
}
