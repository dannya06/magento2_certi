<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class FeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $config['amasty'] = [
            'extrafee' => [
                'enabledOnCheckout' => true,
                'enabledOnCart' => $this->configProvider->isShowOnCart(),
                'displayPriceModeTotal' => $this->configProvider->displayCartPrices(),
                'displayPriceModeBlock' => $this->configProvider->displayCartPrices(),
            ]
        ];
        return $config;
    }
}
