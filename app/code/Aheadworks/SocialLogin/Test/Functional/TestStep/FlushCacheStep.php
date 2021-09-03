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
namespace Aheadworks\SocialLogin\Test\TestStep;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Class FlushCacheStep
 */
class FlushCacheStep implements TestStepInterface
{
    /**
     * @var AdminCache
     */
    protected $adminCache;

    /**
     * @param AdminCache $adminCache
     */
    public function __construct(
        AdminCache $adminCache
    ) {
        $this->adminCache = $adminCache;
    }

    /**
     * Flush cache
     *
     * @return void
     */
    public function run()
    {
        // Flush cache
        $this->adminCache->open();
        $this->adminCache->getActionsBlock()->flushMagentoCache();
        $this->adminCache->getMessagesBlock()->waitSuccessMessage();
    }
}
