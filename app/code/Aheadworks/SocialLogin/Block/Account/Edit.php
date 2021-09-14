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
namespace Aheadworks\SocialLogin\Block\Account;

/**
 * Class Edit
 */
class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Aheadworks\SocialLogin\Helper\State
     */
    private $stateHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aheadworks\SocialLogin\Helper\State $stateHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aheadworks\SocialLogin\Helper\State $stateHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->stateHelper = $stateHelper;
    }

    /**
     * Get firstname
     *
     * @return string
     * @throws \Aheadworks\SocialLogin\Exception\InvalidStateException
     */
    public function getFirstnameValue()
    {
        return $this->stateHelper->getAccount()->getFirstName();
    }

    /**
     * Get lastname value
     *
     * @return string
     * @throws \Aheadworks\SocialLogin\Exception\InvalidStateException
     */
    public function getLastnameValue()
    {
        return $this->stateHelper->getAccount()->getLastName();
    }

    /**
     * Get email value
     *
     * @return string
     * @throws \Aheadworks\SocialLogin\Exception\InvalidStateException
     */
    public function getEmailValue()
    {
        return $this->stateHelper->getAccount()->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->stateHelper->isAccountExist() ? parent::_toHtml() : '';
    }
}
