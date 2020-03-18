<?php
namespace Icube\Cashback\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cashback extends Command
{
    protected $storeCredit;


    public function __construct(
      \Icube\Cashback\Model\StoreCredit $storeCredit,
      array $commands = []
    ){
      $this->storeCredit = $storeCredit;
      parent::__construct();
    }

   protected function configure()
   {
       $this->setName('icube_cashback:create_store_credit');
       $this->setDescription('Create Store Credit Transaction');
       
       parent::configure();
   }
   protected function execute(InputInterface $input, OutputInterface $output)
   {
       $this->storeCredit->createTransaction();
   }
}