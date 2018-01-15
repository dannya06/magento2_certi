<?php
namespace Magento\Framework\Locale\ListsInterface;

/**
 * Proxy class for @see \Magento\Framework\Locale\ListsInterface
 */
class Proxy implements \Magento\Framework\Locale\ListsInterface, \Magento\Framework\ObjectManager\NoninterceptableInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Framework\\Locale\\ListsInterface', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['_subject', '_isShared', '_instanceName'];
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\Framework\Locale\ListsInterface
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionTimezones()
    {
        return $this->_getSubject()->getOptionTimezones();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionWeekdays($preserveCodes = false, $ucFirstCode = false)
    {
        return $this->_getSubject()->getOptionWeekdays($preserveCodes, $ucFirstCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionCountries()
    {
        return $this->_getSubject()->getOptionCountries();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionCurrencies()
    {
        return $this->_getSubject()->getOptionCurrencies();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionAllCurrencies()
    {
        return $this->_getSubject()->getOptionAllCurrencies();
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryTranslation($value, $locale = null)
    {
        return $this->_getSubject()->getCountryTranslation($value, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionLocales()
    {
        return $this->_getSubject()->getOptionLocales();
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatedOptionLocales()
    {
        return $this->_getSubject()->getTranslatedOptionLocales();
    }
}
