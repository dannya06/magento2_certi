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
        
        $applied_rule_ids = $quote->getData("applied_rule_ids");
  
        $applied_rule_ids = explode(",",$applied_rule_ids);
        foreach($applied_rule_ids as $applied_rule_id){
            $sales_rule_id = $this->salesRule->load($applied_rule_id);
            if($applied_rule_id == $sales_rule_id->getData("rule_id")){
                if($sales_rule_id->getData("is_active") == 0){
                    if(is_array($applied_rule_ids)){
                        $applied_rule_ids = array_diff($applied_rule_ids, [$applied_rule_id]);
                        $applied_rule_ids = implode(",", $applied_rule_ids);
                        if(!empty($applied_rule_ids)){
                            $sql = "UPDATE " . $tableName . " SET applied_rule_ids='".$applied_rule_ids."' WHERE entity_id = '".$quote->getData("entity_id")."'";$connection->query($sql);
                        }
                    }
                }
            }
        }

        $sql = "UPDATE " . $tableName . " SET trigger_recollect=0 WHERE entity_id = '".$quote->getData("quote_id_1")."'";$connection->query($sql);

    }
}
