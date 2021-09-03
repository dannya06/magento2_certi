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
namespace Aheadworks\SocialLogin\Block\Adminhtml\System\Config\Form\Field\LoginBlock;

/**
 * Class Visibility
 */
class Visibility extends \Aheadworks\SocialLogin\Block\Adminhtml\System\Config\Form\Field\Renderer\Select
{
    /**
     * @var \Aheadworks\SocialLogin\Model\Config\Source\LoginBlock\Visibility
     */
    protected $visibilitySource;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Aheadworks\SocialLogin\Model\Config\Source\LoginBlock\Visibility $visibilitySource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Aheadworks\SocialLogin\Model\Config\Source\LoginBlock\Visibility $visibilitySource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->visibilitySource = $visibilitySource;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->visibilitySource->toOptionArray();
    }
}
