<?php

namespace Icube\BankList\Block\Adminhtml\Config;

class HtmlYesNo extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $configYesno;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Config\Model\Config\Source\Yesno $configYesno
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $configYesno,
        array $data = []
    ) {
        $this->configYesno = $configYesno;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    protected function _toHtml()
    {
        $inputId = $this->getInputId();
        $inputName = $this->getInputName();
        $colName = $this->getColumnName();
        $column = $this->getColumn();

        $string = '<select id="' . $inputId . '"' .
            ' name="' . $inputName . '" <%- ' . $colName . ' %> ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') .
            ($column['style'] ? 'style="' . $column['style'] . '"' : '') .
            ' class="' . (isset($column['class']) ? $column['class'] : 'input-text') . '">';
        foreach ($this->configYesno->toOptionArray() as $row) {
            $string .= '<option value="' . $row['value'] . '">' . $row['label'] . '</option>';
        }
        $string .= '</select>';

        return $string;
    }
}
