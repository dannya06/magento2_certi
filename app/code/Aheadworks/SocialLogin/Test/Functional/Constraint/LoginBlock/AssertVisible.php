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
namespace Aheadworks\SocialLogin\Test\Constraint\LoginBlock;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Page\FrontendPage;

/**
 * Class AssertVisible
 */
abstract class AssertVisible extends AbstractConstraint
{
    /**
     * Default login block name
     */
    const DEFAULT_BLOCK_NAME = 'socialLoginBlock';

    /**
     * @var string
     */
    protected $blockName = self::DEFAULT_BLOCK_NAME;

    /**
     * Process is visible block assert
     *
     * @param FrontendPage $loginPage
     */
    protected function processVisibleAssert(FrontendPage $loginPage)
    {
        $loginPage->open();
        $isBlockVisible = $this->getPageBlock($loginPage)->isVisible();

        \PHPUnit_Framework_Assert::assertTrue(
            $isBlockVisible,
            'Social account doesn\'t visible.'
        );
    }

    /**
     * @param FrontendPage $page
     * @return \Magento\Mtf\Block\BlockInterface
     */
    protected function getPageBlock(FrontendPage $page)
    {
        return $page->getBlockInstance($this->blockName);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return 'Social account doesn\'t visible on.';
    }
}
