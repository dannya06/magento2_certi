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
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Model\Filters;

use Aheadworks\StoreCredit\Model\Filters\Transaction\CustomerSelection;
use Magento\Framework\Filter\AbstractFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

/**
 * Class Aheadworks\StoreCredit\Model\Filters\Factory
 */
class Factory extends AbstractFactory
{
    /**
     * @var array
     */
    protected $invokableClasses = [
        'date' => Date::class,
        'aw_storecredit_custselect' => CustomerSelection::class,
    ];
}
