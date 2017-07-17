<?php
namespace WeltPixel\Sitemap\Model\Rewrite;

/**
 * Class Sitemap
 * @package WeltPixel\Sitemap\Model\Rewrite
 */
class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * @return array
     */
    public function getSitemapItems() {
        return $this->_sitemapItems;
    }

    /**
     * @param \Magento\Framework\DataObject $sitemapItem
     * @return $this
     */
    public function addSitemapItem($sitemapItem) {
        $this->_sitemapItems[] = $sitemapItem;

        return $this;
    }

}
