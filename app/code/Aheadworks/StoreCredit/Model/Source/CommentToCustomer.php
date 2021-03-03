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
namespace Aheadworks\StoreCredit\Model\Source;

use Aheadworks\StoreCredit\Model\Comment\CommentPool;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Aheadworks\StoreCredit\Model\Source\CommentToCustomer
 */
class CommentToCustomer implements ArrayInterface
{
    /**
     * @var CommentPool
     */
    private $commentPool;

    /**
     * @var array
     */
    private $comments;

    /**
     * @param CommentPool $commentPool
     */
    public function __construct(CommentPool $commentPool)
    {
        $this->commentPool = $commentPool;
    }

    /**
     *  {@inheritDoc}
     */
    public function toOptionArray()
    {
        if ($this->comments == null) {
            $this->comments = [];
            foreach ($this->commentPool->getAllComments() as $commentInstance) {
                $this->comments[] = [
                    'value' => $commentInstance->getComment(),
                    'label' => $commentInstance->getLabel()
                ];
            }
        }
        return $this->comments;
    }
}
