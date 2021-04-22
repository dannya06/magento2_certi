<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class DisplayTax extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $url = $this->getUrl('adminhtml/system_config/edit/section/amasty_extrafee');
        $element->setComment($this->getCommentMessage($url));

        return parent::render($element);
    }

    /**
     * @param string $url
     * @return \Magento\Framework\Phrase
     */
    private function getCommentMessage($url)
    {
        return __(
            "More Extra Fee related options are available at extension's "
            . "<a href='%1' onclick=\"return confirm('Unsaved changes will be discarded.')\">configuration page</a>.",
            $url
        );
    }
}
