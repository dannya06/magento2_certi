<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api;

interface GuestFeesInformationManagementInterface
{
    /**
     * @param string $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     *
     * @return \Amasty\Extrafee\Api\Data\FeesManagerInterface
     */
    public function collect(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    );
}
