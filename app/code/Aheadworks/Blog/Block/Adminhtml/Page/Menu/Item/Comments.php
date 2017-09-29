<?php
namespace Aheadworks\Blog\Block\Adminhtml\Page\Menu\Item;

/**
 * 'Comments' page menu item
 * @package Aheadworks\Blog\Block\Adminhtml\Page\Menu\Item
 * @codeCoverageIgnore
 */
class Comments extends \Aheadworks\Blog\Block\Adminhtml\Page\Menu\Item
{
    /**
     * @var \Aheadworks\Blog\Model\Disqus
     */
    private $disqus;

    /**
     * Comments constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Aheadworks\Blog\Model\Disqus $disqus
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Aheadworks\Blog\Model\Disqus $disqus,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->disqus = $disqus;
    }

    /**
     * @inheritdoc
     */
    protected function prepareLinkAttributes()
    {
        parent::prepareLinkAttributes();
        $linkAttributes = $this->getLinkAttributes();
        $linkAttributes['href'] = $this->disqus->getAdminUrl();
        $this->setLinkAttributes($linkAttributes);
    }
}
