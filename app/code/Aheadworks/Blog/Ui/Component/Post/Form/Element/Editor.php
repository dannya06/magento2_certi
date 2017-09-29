<?php
namespace Aheadworks\Blog\Ui\Component\Post\Form\Element;

use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Editor
 * @package Aheadworks\Blog\Ui\Component\Post\Form\Element
 */
class Editor extends Textarea
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    private $wysiwygConfig;

    /**
     * Editor constructor.
     *
     * @param ContextInterface $context
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if (!isset($config['wysiwygConfig'])) {
            $config['wysiwygConfig'] = $this->wysiwygConfig->getConfig()->toArray();
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
