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
namespace Aheadworks\SocialLogin\Model\Provider\Factory;

use Aheadworks\SocialLogin\Model\Config\ProviderInterface as ProviderConfig;
use Aheadworks\SocialLogin\Model\Provider\ServiceBuilderInterface;

/**
 * Base provider factory
 */
class Base extends \Aheadworks\SocialLogin\Model\Provider\AbstractFactory
{
    /**
     * @var ServiceBuilderInterface
     */
    protected $serviceBuilder;

    /**
     * @var ProviderConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected $loginRequestProcessorType;

    /**
     * @var string
     */
    protected $callbackRequestProcessorType;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ProviderConfig $config
     * @param ServiceBuilderInterface $serviceBuilder
     * @param string $loginRequestProcessorType
     * @param string $callbackRequestProcessorType
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ProviderConfig $config,
        ServiceBuilderInterface $serviceBuilder,
        $loginRequestProcessorType,
        $callbackRequestProcessorType
    ) {
        parent::__construct($objectManager);
        $this->serviceBuilder = $serviceBuilder;
        $this->config = $config;
        $this->loginRequestProcessorType = $loginRequestProcessorType;
        $this->callbackRequestProcessorType = $callbackRequestProcessorType;
    }

    /**
     * {@inheritdoc}
     */
    public function createService()
    {
        return $this->serviceBuilder->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function createCallbackRequestProcessor()
    {
        return $this->objectManager->create($this->callbackRequestProcessorType);
    }

    /**
     * {@inheritdoc}
     */
    public function createLoginRequestProcessor()
    {
        return $this->objectManager->create($this->loginRequestProcessorType);
    }
}
