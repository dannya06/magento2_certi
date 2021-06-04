<?php

namespace Icube\PaymentReminder\Console\Command;
 
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 
class SyncTabelorder extends Command
{   
    protected function configure()
    {
        $this->setName('icube:paymentreminder:sync')
            ->setDescription('Sync from tabel order');
        parent::configure();
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $om->get('\Magento\Framework\App\State')->setAreaCode('adminhtml');
        $output->writeln('<info>Getting All Pending Order</info>');
        $om->get('\Icube\PaymentReminder\Helper\CancelOrder')->migrateData();
        $output->writeln('<info>Finish</info>');
    }
}