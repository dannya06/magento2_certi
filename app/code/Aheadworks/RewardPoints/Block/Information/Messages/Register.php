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
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Block\Information\Messages;

/**
 * Class Register
 *
 * @package Aheadworks\RewardPoints\Block\Information\Messages
 */
class Register extends AbstractMessages
{
    /**
     * {@inheritdoc}
     */
    public function canShow()
    {
        return $this->config->getFrontendIsDisplayInvitationToRegister() && $this->getEarnPoints()
            && !$this->isCustomerLoggedIn();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        $earnPoints = $this->getEarnPoints();
        return __(
            'Register now to earn <strong>%1 points%2</strong>. <a href="%3">Learn more</a>.',
            $earnPoints,
            $this->getEarnMoneyByPoints($earnPoints),
            $this->getFrontendExplainerPageLink()
        );
    }

    /**
     * Retrieve how much points will be earned
     *
     * @return int
     */
    public function getEarnPoints()
    {
        return $this->config->getAwardedPointsForRegistration();
    }
}
