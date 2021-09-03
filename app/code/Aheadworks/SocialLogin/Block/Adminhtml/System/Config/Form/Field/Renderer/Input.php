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
namespace Aheadworks\SocialLogin\Block\Adminhtml\System\Config\Form\Field\Renderer;

/**
 * HTML input element block
 */
class Input extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Get additional attributes
     *
     * @return string
     */
    protected function getAdditionalAttributes()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $column = $this->getColumn();

        return '<input type="text" id="' . $this->getInputId() .
            '"' .
            $this->getAdditionalAttributes() .
            ' name="' .
            $this->getInputName() .
            '" value="<%- ' .
            $this->getColumnName() .
            ' %>" ' .
            ($column['size'] ? 'size="' .
                $column['size'] .
                '"' : '') .
            ' class="' .
            (isset(
                $column['class']
            ) ? $column['class'] : 'input-text') . '"' . (isset(
                $column['style']
            ) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
