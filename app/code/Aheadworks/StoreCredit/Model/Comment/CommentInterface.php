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
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Model\Comment;

/**
 * Interface Aheadworks\StoreCredit\Model\Comment\CommentInterface
 */
interface CommentInterface
{
    /**
     * Retrieve comment type
     *
     * @return int
     */
    public function getType();

    /**
     * Retrieve comment label
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    public function getLabel($key = null, $arguments = []);

    /**
     * Render comment key to comment label
     *
     * @param array $arguments
     * @param string $key
     * @param string $label
     * @param bool $renderingUrl
     * @param bool $frontend
     * @return string
     */
    public function renderComment(
        $arguments = [],
        $key = null,
        $label = null,
        $renderingUrl = false,
        $frontend = false
    );
}
