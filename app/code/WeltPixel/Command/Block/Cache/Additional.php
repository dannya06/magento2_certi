<?php
namespace WeltPixel\Command\Block\Cache;

class Additional extends \Magento\Backend\Block\Template
{
    /**
     * @return string
     */
    public function getCleanLessUrl()
    {
        return $this->getUrl('weltpixelcommand/cache/cleanLess');
    }
}
