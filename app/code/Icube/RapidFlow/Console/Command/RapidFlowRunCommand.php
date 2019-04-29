<?php

namespace Icube\RapidFlow\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Unirgy\RapidFlow\Helper\Data as RapidFlowHelper;

class RapidFlowRunCommand extends Command
{
    const PROFILEID_ARGUMENT = 'profileId';

    /**
     * @var RapidFlowHelper
     */
    protected $rapidFlowHelper;

    public function __construct(RapidFlowHelper $rapidFlowHelper)
    {
        $this->rapidFlowHelper = $rapidFlowHelper;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('rapidflow:run')
            ->setDescription('Run RapidFlow Profile')
            ->setDefinition([
                new InputArgument(
                    self::PROFILEID_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Profile ID'
                )
            ])
        ;

        return parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $profileId = $input->getArgument(self::PROFILEID_ARGUMENT);
        if (is_null($profileId)) {
            throw new \InvalidArgumentException('Argument ' . self::PROFILEID_ARGUMENT . ' is missing.');
        }

        $this->rapidFlowHelper->run($profileId);
    }
}
