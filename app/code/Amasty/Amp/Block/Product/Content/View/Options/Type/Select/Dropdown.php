<?php

namespace Amasty\Amp\Block\Product\Content\View\Options\Type\Select;

use Magento\Framework\Data\CollectionDataSourceInterface;

class Dropdown extends AbstractSelect implements CollectionDataSourceInterface
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Amp::product/content/view/options/type/select/dropdown.phtml';
}
