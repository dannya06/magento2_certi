<?php
namespace NS8\CSP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\DB\Ddl\Table;
use NS8\CSP\Helper\Logger;

class Setup extends AbstractHelper
{
    private $logger;
    private $restClient;
    private $configHelper;
    private $timezone;

    public function __construct(
        Logger $logger,
        \NS8\CSP\Helper\Config $configHelper,
        \NS8\CSP\Helper\RESTClient $restClient,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->restClient = $restClient;
        $this->timezone = $timezone;
    }

    //  Install/upgrade db schema
    public function setupSchema($setup)
    {
        $this->configHelper->setAdminAreaCode();

        $tableName = $setup->getTable('ns8_sales_order_sync');
        $connection = $setup->getConnection();

        // Check if the table already exists
        if ($connection->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                           ->newTable($tableName)
                           ->addColumn(
                               'id',
                               Table::TYPE_INTEGER,
                               null,
                               [
                                   'unsigned' => true,
                                   'nullable' => false,
                                   'identity' => true,
                                   'primary' => true
                               ],
                               'Order Sync Id'
                           )
                           ->addColumn(
                               'order_id',
                               Table::TYPE_INTEGER,
                               null,
                               [
                                   'identity' => false,
                                   'unsigned' => true,
                                   'nullable' => false
                               ],
                               'Order Id'
                           )
                           ->addColumn(
                               'increment_id',
                               Table::TYPE_TEXT,
                               32,
                               ['nullable' => false],
                               'Increment Id'
                           )
                           ->addColumn(
                               'created_at',
                               Table::TYPE_TIMESTAMP,
                               null,
                               ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                               'Created At'
                           )
                           ->addColumn(
                               'status',
                               Table::TYPE_SMALLINT,
                               null,
                               ['nullable' => false, 'default' => 0],
                               'Status'
                           )
                           ->addColumn(
                               'failures',
                               Table::TYPE_SMALLINT,
                               null,
                               ['nullable' => false, 'default' => 0],
                               'Failures'
                           )
                           ->addColumn(
                               'order_status_set',
                               Table::TYPE_SMALLINT,
                               null,
                               ['nullable' => false, 'default' => 0],
                               'Order Status Set'
                           )
                           ->addIndex(
                               $setup->getIdxName(
                                   $tableName,
                                   ['order_id'],
                                   \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                               ),
                               ['order_id'],
                               ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                           )
                           ->addIndex(
                               $setup->getIdxName(
                                   $tableName,
                                   ['increment_id'],
                                   \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                               ),
                               ['increment_id'],
                               ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                           )
                           ->addIndex(
                               $setup->getIdxName(
                                   $tableName,
                                   ['status', 'created_at']
                               ),
                               ['status', 'created_at']
                           )
                           ->addIndex(
                               $setup->getIdxName(
                                   $tableName,
                                   ['created_at']
                               ),
                               ['created_at']
                           )
                           ->setComment('NS8 CSP Sales Order Status');

            $connection->createTable($table);
        }

        $connection->addColumn(
            $setup->getTable('sales_order'),
            'eq8_score',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'EQ8 Score'
            ]
        );

        $connection->addColumn(
            $setup->getTable('sales_order_grid'),
            'eq8_score',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'EQ8 Score'
            ]
        );

        $connection->addColumn(
            $setup->getTable('sales_order'),
            'ns8_status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 64,
                'nullable' => true,
                'comment' => 'NS8 Status'
            ]
        );

        $connection->addColumn(
            $setup->getTable('sales_order_grid'),
            'ns8_status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 64,
                'nullable' => true,
                'comment' => 'NS8 Status'
            ]
        );

        $connection->addIndex(
            $setup->getTable('sales_order'),
            $setup->getIdxName('sales_order', ['ns8_status']),
            ['ns8_status']
        );

        $connection->addIndex(
            $setup->getTable('sales_order'),
            $setup->getIdxName('sales_order', ['eq8_score']),
            ['eq8_score']
        );

        $connection->addIndex(
            $setup->getTable('sales_order_grid'),
            $setup->getIdxName('sales_order_grid', ['ns8_status']),
            ['ns8_status']
        );

        $connection->addIndex(
            $setup->getTable('sales_order_grid'),
            $setup->getIdxName('sales_order_grid', ['eq8_score']),
            ['eq8_score']
        );
    }

    //  create/update account
    public function setupAccount()
    {
        $params = [
            "shops" => $this->configHelper->getStores(),
            "email" => $this->configHelper->getStoreEmail(),
            "timezone" => $this->timezone->getConfigTimezone(),
            "accessToken" => $this->configHelper->getAccessToken()
        ];

        $response = $this->restClient->post("protect/magento/install", $params, null, 30);

        if (!isset($response)) {
            $this->logger->error('hostUpgrade', 'Unable to create account. No response from api', $params);
            return;
        }

        if ($response->getStatus() != 200) {
            $this->logger->error('hostUpgrade', $response->getMessage(), $params);
            return;
        }

        $this->configHelper->setProjectId($response->data->projectId);
        $this->configHelper->setAccessToken($response->data->accessToken);
    }
}
