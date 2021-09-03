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
namespace Aheadworks\SocialLogin\Test\Block;

use Magento\Mtf\Block\Form;

/**
 * Class ProviderLoginForm
 */
abstract class ProviderLoginForm extends Form
{
    /**
     * @var string
     */
    protected $submitButtonSelector = '';

    /**
     * Fill credentials data
     *
     * @param array $data
     * @throws \Exception
     */
    public function fillCredentials($data)
    {
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping);
    }
    /**
     * Click allow
     */
    public function clickAllow()
    {
        $this->_rootElement->find($this->submitButtonSelector)->click();
    }
}
