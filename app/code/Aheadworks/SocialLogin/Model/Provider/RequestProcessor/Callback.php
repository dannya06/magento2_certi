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

use Aheadworks\SocialLogin\Model\Provider\Account\RetrieverInterface;
use Aheadworks\SocialLogin\Model\Provider\AccountInterface;
use Aheadworks\SocialLogin\Model\Provider\Service\ServiceInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Callback request processor
 */
abstract class Callback implements CallbackInterface
{
    /**
     * @var RetrieverInterface
     */
    protected $accountRetriever;

    /**
     * @var AccountInterface
     */
    protected $account;

    /**
     * @param RetrieverInterface $accountRetriever
     */
    public function __construct(RetrieverInterface $accountRetriever)
    {
        $this->accountRetriever = $accountRetriever;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServiceInterface $service, RequestInterface $request)
    {
        $this->processRequest($service, $request);
        return $this->retrieveData($service);
    }

    /**
     * Process request
     *
     * @param ServiceInterface $service
     * @param RequestInterface $request
     * @return mixed
     */
    abstract protected function processRequest(ServiceInterface $service, RequestInterface $request);

    /**
     * Retrieve data
     *
     * @param ServiceInterface $service
     * @return AccountInterface
     */
    protected function retrieveData(ServiceInterface $service)
    {
        return $this->accountRetriever->retrieve($service);
    }
}
