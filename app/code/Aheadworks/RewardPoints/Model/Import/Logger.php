<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Model\Import;

/**
 * Class Logger
 * @package Aheadworks\RewardPoints\Model\Import
 */
class Logger
{
    /**
     * @var \Zend\Log\Logger
     */
    private $logger;

    /**
     * Initialize logger
     *
     * @param string $filename
     * @return $this
     */
    public function init($filename)
    {
        $writer = new \Zend\Log\Writer\Stream($filename);
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);

        return $this;
    }

    /**
     * Add message to log
     *
     * @param $message
     * @return $this
     */
    public function addMessage($message)
    {
        $this->logger->info($message);

        return $this;
    }
}
