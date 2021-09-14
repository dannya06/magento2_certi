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
 * @package    SocialLogin
 * @version    1.6.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\SocialLogin\Model\LoginBlock\Template;

use Aheadworks\SocialLogin\Exception\InvalidTemplateException;
use Aheadworks\SocialLogin\Model\LoginBlock\Template;

/**
 * Class Provider
 */
class Provider
{
    /**
     * Templates data
     *
     * @var array
     */
    protected $templatesData;

    /**
     * @param array $templatesData
     */
    public function __construct(
        array $templatesData = []
    ) {
        $this->templatesData = $templatesData;
    }

    /**
     * Get template instance
     *
     * @param string $id
     * @return Template
     * @throws InvalidTemplateException
     */
    public function getTemplateInstance($id)
    {
        if (!$this->isTemplateExist($id)) {
            throw new InvalidTemplateException(__('Template %1 undefined', $id));
        }
        return $this->templatesData[$id]['template_instance'];
    }

    /**
     * Get templates data
     *
     * @return array
     */
    public function getTemplatesData()
    {
        return $this->templatesData;
    }

    /**
     * Is template exist
     *
     * @param string $id
     * @return bool
     */
    protected function isTemplateExist($id)
    {
        return isset($this->templatesData[$id]['template_instance'])
            && $this->templatesData[$id]['template_instance'] instanceof Template;
    }
}
