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
namespace Aheadworks\SocialLogin\Block\Element\Html\Link;

use \Aheadworks\SocialLogin\Block\Element\Template\VisibilityTrait;

/**
 * Class Current
 */
class Current extends \Magento\Framework\View\Element\Html\Link\Current
{
    use VisibilityTrait;

    /**
     * @var \Aheadworks\SocialLogin\Model\Config\General
     */
    protected $moduleConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Aheadworks\SocialLogin\Model\Config\General $moduleConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Aheadworks\SocialLogin\Model\Config\General $moduleConfig,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @return \Aheadworks\SocialLogin\Model\Config\General
     */
    protected function getModuleConfig()
    {
        return $this->moduleConfig;
    }
}
