<?php

namespace Icube\Logistix\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetLogistix extends Command
{
    protected function configure()
    {
        $this->setName('icube:logistix:createorder')
            ->setDescription('Get Logistix');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $om->get('Magento\Framework\App\State')->setAreaCode('adminhtml');
        $helper = $om->get('\Icube\Logistix\Helper\Data');
        $helper->getLogistix();       
    }
}