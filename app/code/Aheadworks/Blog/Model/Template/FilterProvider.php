<?php
namespace Aheadworks\Blog\Model\Template;

/**
 * Template filter provider
 */
class FilterProvider
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $filterClassName;

    /**
     * @var \Magento\Framework\Filter\Template|null
     */
    private $filterInstance = null;

    /**
     * FilterProvider constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $filterClassName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $filterClassName = 'Magento\Cms\Model\Template\Filter'
    ) {
        $this->objectManager = $objectManager;
        $this->filterClassName = $filterClassName;
    }

    /**
     * Retrieves filter instance
     *
     * @return \Magento\Framework\Filter\Template|mixed|null
     * @throws \Exception
     */
    public function getFilter()
    {
        if ($this->filterInstance === null) {
            $filterInstance = $this->objectManager->get($this->filterClassName);
            if (!$filterInstance instanceof \Magento\Framework\Filter\Template) {
                throw new \Exception(
                    'Template filter ' . $this->filterClassName . ' does not implement required interface.'
                );
            }
            $this->filterInstance = $filterInstance;
        }
        return $this->filterInstance;
    }
}
