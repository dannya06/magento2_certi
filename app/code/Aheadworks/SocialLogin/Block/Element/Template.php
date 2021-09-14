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
namespace Aheadworks\SocialLogin\Block\Element;

/**
 * Class Template
 */
class Template extends \Magento\Framework\View\Element\Template
{
    use Template\VisibilityTrait;

    const THEME_CSS_CLASS = 'social-login-theme-%s';

    /**
     * @var \Aheadworks\SocialLogin\Model\Config\General
     */
    protected $moduleConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aheadworks\SocialLogin\Model\Config\General $moduleConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aheadworks\SocialLogin\Model\Config\General $moduleConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @return \Aheadworks\SocialLogin\Model\Config\General
     */
    protected function getModuleConfig()
    {
        return $this->moduleConfig;
    }

    /**
     * Get theme css class
     *
     * @return string
     */
    public function getThemeCssClass()
    {
        $themeCode = $this->_design->getDesignTheme()->getCode();
        $themeCode = str_replace(['/', ' '], '_', $themeCode);
        $themeCode = strtolower($themeCode);
        return sprintf(self::THEME_CSS_CLASS, $themeCode);
    }
}
