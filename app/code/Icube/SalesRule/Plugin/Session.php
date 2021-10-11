<?php

namespace Icube\SalesRule\Plugin;

class Session
{
    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $salesRule;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $quoteSession;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\SalesRule\Model\Rule $salesRule
     * @param \Magento\Checkout\Model\Session $quoteSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\SalesRule\Model\Rule $salesRule,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Checkout\Model\Session $quoteSession
    ) {
        $this->salesRule = $salesRule;
        $this->resource = $resource;
        $this->quoteSession = $quoteSession;
    }

    public function beforeGetQuote()
    {    
        $quote = $this->quoteSession;
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tableName = $this->resource->getTableName('quote');
        
        $sql = "UPDATE " . $tableName . " SET trigger_recollect=0 WHERE entity_id = '".$quote->getData("quote_id_1")."'";$connection->query($sql);

    }
}
