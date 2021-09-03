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
namespace Aheadworks\SocialLogin\Exception;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerConvertException
 */
class CustomerConvertException extends LocalizedException
{
    /**
     * @var
     */
    private $errors;

    /**
     * @param \Magento\Framework\Phrase $phrase
     * @param array $errors
     * @param \Exception|null $cause
     */
    public function __construct(
        \Magento\Framework\Phrase $phrase,
        array $errors,
        \Exception $cause = null
    ) {
        parent::__construct($phrase, $cause);
        $this->errors = $errors;
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
