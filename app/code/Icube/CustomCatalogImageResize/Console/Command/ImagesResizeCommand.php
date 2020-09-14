<?php

namespace Icube\CustomCatalogImageResize\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\MediaStorage\Service\ImageResize;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImagesResizeCommand extends \Symfony\Component\Console\Command\Command
{
    private $resize;
    private $appState;
    private $progressBarFactory;

    public function __construct(
        State $appState,
        ImageResize $resize,
        ObjectManagerInterface $objectManager,
        ProgressBarFactory $progressBarFactory = null
    ) {
        parent::__construct();
        $this->resize = $resize;
        $this->appState = $appState;
        $this->progressBarFactory = $progressBarFactory
            ?: ObjectManager::getInstance()->get(ProgressBarFactory::class);
    }

    protected function configure()
    {
        $this->setName('icube:catalog:images:resize')
            ->setDescription('Creates resized product images by Icube');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $errors = [];
            $this->appState->setAreaCode(Area::AREA_GLOBAL);
            $generator = $this->resize->resizeFromThemes();

            $progress = $this->progressBarFactory->create(
                [
                    'output' => $output,
                    'max' => $generator->current()
                ]
            );
            $progress->setFormat(
                "%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s% \t| <info>%message%</info>"
            );

            if ($output->getVerbosity() !== OutputInterface::VERBOSITY_NORMAL) {
                $progress->setOverwrite(false);
            }

            while ($generator->valid()) {
                $resizeInfo = $generator->key();
                $error = $resizeInfo['error'];
                $filename = $resizeInfo['filename'];

                if ($error !== '') {
                    $errors[$filename] = $error;
                }

                $progress->setMessage($filename);
                $progress->advance();
                $generator->next();
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $output->write(PHP_EOL);
        if (count($errors)) {
            $output->writeln("<info>Some product images resized successfully.\nBut you've to fix ".count($errors)." errors.\nSee details: /var/log/exception-imageresize.log</info>");
            foreach ($errors as $error) {
                $arrErr[] = array('Fix: ' => $error);
            }
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/exception-imageresize.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($arrErr);  
        } else {
            $output->writeln("<info>Product images resized successfully</info>");
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
