<?php
namespace Aheadworks\Blog\Ui\Component\Post\Form\Element;

use Magento\Ui\Component\Form\Element\Input;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class CommentsLink
 * @package Aheadworks\Blog\Ui\Component\Post\Form\Element
 */
class CommentsLink extends Input
{
    /**
     * @var \Aheadworks\Blog\Model\Disqus
     */
    private $disqus;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * CommentsLink constructor.
     *
     * @param ContextInterface $context
     * @param \Aheadworks\Blog\Model\Disqus $disqus
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Aheadworks\Blog\Model\Disqus $disqus,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->disqus = $disqus;
        $this->authSession = $authSession;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if (!isset($config['url'])
            && $this->authSession->isAllowed('Aheadworks_Blog::comments')
        ) {
            $config['url'] = $this->disqus->getAdminUrl();
            $config['linkLabel'] = __('Go To Comments');
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
