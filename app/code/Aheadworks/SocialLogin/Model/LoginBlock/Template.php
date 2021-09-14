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
namespace Aheadworks\SocialLogin\Model\LoginBlock;

/**
 * Class Template
 */
class Template
{
    /**
     * Template path
     *
     * @var string
     */
    protected $path;

    /**
     * Additional data
     *
     * @var array
     */
    protected $additionalData;

    /**
     * @param string $path
     * @param array $additionalData
     */
    public function __construct(
        $path,
        array $additionalData = []
    ) {
        $this->path = $path;
        $this->additionalData = $additionalData;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get additional data
     *
     * @return array
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }
}
