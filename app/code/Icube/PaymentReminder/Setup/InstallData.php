<?php

namespace Icube\PaymentReminder\Setup;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    protected $salesSetupFactory;

    public function __construct(
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function install(\Magento\Framework\Setup\ModuleDataSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        /*$salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $options = ['type' => 'varchar', 'visible' => false, 'required' => false];
        $salesSetup->addAttribute('order', 'email_notification', $options);*/
    }
}