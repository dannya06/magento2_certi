<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\CustomStyle\Block\Cache;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Permissions
 */
class Permissions implements ArgumentInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * Permissions constructor.
     *
     * @param AuthorizationInterface $authorization
     */
    public function __construct(AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * @return bool
     */
    public function hasAccessToClearVarnish()
    {
        return $this->authorization->isAllowed('Icube_CustomStyle::clear_varnish');
    }
    /**
     * @return bool
     */
    public function hasAccessToReneregateStatic()
    {
        return $this->authorization->isAllowed('Icube_CustomStyle::regenerate_static');
    }
    /**
     * @return bool
     */
    public function hasAccessToClearPageCache()
    {
        return $this->authorization->isAllowed('Icube_CustomStyle::clear_fpc');
    }
    /**
     * @return bool
     */
    public function hasAccessToCustomAdditionalActions()
    {
        return ($this->hasAccessToClearVarnish()
                || $this->hasAccessToReneregateStatic()
                || $this->hasAccessToAdditionalActions());
    }
}
