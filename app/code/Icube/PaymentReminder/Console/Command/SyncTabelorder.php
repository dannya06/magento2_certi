<?php

namespace Icube\PaymentReminder\Console\Command;
 
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 
class SyncTabelorder extends Command
{   
    protected $objectManager;
    protected $cancelorder;
 
    public function __construct(
        \Magento\Framework\App\ObjectManagerFactory $objectManagerFactory,
        \Magento\Framework\App\State $state,
        \Icube\PaymentReminder\Helper\CancelOrder $cancelorder
    ){
        $params = $_SERVER;
        $params[StoreManager::PARAM_RUN_CODE] = 'admin';
        $params[StoreManager::PARAM_RUN_TYPE] = 'store';
        $this->objectManager = $objectManagerFactory->create($params);
        $this->cancelorder = $cancelorder;
        $this->state = $state;
        parent::__construct();
    }
 
    protected function configure()
    {
        $this->setName('icube:paymentreminder:sync')
            ->setDescription('Sync from tabel order');
        parent::configure();
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $output->writeln('<info>Getting All Pending Order</info>');
        $registry = $this->objectManager->get('\Magento\Framework\Registry');
        $registry->register('isSecureArea', true);
        $this->cancelorder->migrateData();
        $output->writeln('<info>Finish</info>');
        return 0;
    }
}