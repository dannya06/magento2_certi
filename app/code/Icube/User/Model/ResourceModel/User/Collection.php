<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\User\Model\ResourceModel\User;

/**
 * Admin user collection
 *
 * @api
 * @since 100.0.2
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    



    /**
     * Event manager proxy
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session = null;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param \Magento\Backend\Model\Auth\Session $session
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        \Magento\Backend\Model\Auth\Session $session = null
    ) {
        $this->_session = $session;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\User\Model\User::class, \Magento\User\Model\ResourceModel\User::class);
    }

    /**
     * Collection Init Select
     *
     * @return $this
     * @since 101.1.0
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        
        if (empty($this->_session)) {
            $this->_session = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Backend\Model\Auth\Session'
            )->getData('user')->getId();
        }

        if($this->_session != "1"){
            $this->getSelect()->joinLeft(
                ['user_role' => $this->getTable('authorization_role')],
                'main_table.user_id = user_role.user_id AND user_role.parent_id != 0',
                []
            )->joinLeft(
                ['detail_role' => $this->getTable('authorization_role')],
                'user_role.parent_id = detail_role.role_id',
                ['role_name']
            )->where('detail_role.role_name != "Administrators"');
        }else{
            $this->getSelect()->joinLeft(
                ['user_role' => $this->getTable('authorization_role')],
                'main_table.user_id = user_role.user_id AND user_role.parent_id != 0',
                []
            )->joinLeft(
                ['detail_role' => $this->getTable('authorization_role')],
                'user_role.parent_id = detail_role.role_id',
                ['role_name']
            );
        }
    }
}
