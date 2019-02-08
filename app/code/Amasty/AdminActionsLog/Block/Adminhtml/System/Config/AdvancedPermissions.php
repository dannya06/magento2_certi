<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;

class AdvancedPermissions extends Field
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(
        Manager $manager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->manager = $manager;
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        if ($this->manager->isEnabled('Amasty_Rolepermissions')) {
            $element->setValue(__('Installed'));
            $element->setHtmlId('amasty_is_instaled');
        } else {
            $element->setValue(__('Not Installed'));
            $element->setHtmlId('amasty_not_instaled');
            $element->setComment(__('Easily manage multi-vendor stores by assigning custom role permissions to '
                . 'specific managers. Let them see and edit only particular products, categories, '
                . 'store views and websites. '
                . 'See more details <a href="https://amasty.com/advanced-permissions-for-magento-2.html'
                . '?utm_source=extension&utm_medium=backend&utm_campaign=from_adminactionlog_to_advpermissions_m2" '
                . 'target="_blank">here.</a>'
            ));
        }

        return parent::render($element);
    }
}
