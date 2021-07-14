<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Api;

use Magento\Checkout\Api\Data\TotalsInformationInterface;

interface GuestFeesInformationManagementInterface
{
    /**
     * @param string $cartId
     * @param TotalsInformationInterface $addressInformation
     *
     * @return \Amasty\Extrafee\Api\Data\FeesManagerInterface
     */
    public function collect(
        $cartId,
        TotalsInformationInterface $addressInformation
    );
}
