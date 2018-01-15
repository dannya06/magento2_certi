<?php
namespace Wyomind\Core\Helper\Data;

/**
 * Proxy class for @see \Wyomind\Core\Helper\Data
 */
class Proxy extends \Wyomind\Core\Helper\Data implements \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Wyomind\Core\Helper\Data
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Wyomind\\Core\\Helper\\Data', $shared = true)
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
     * @return \Wyomind\Core\Helper\Data
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
    public function getMagentoVersion()
    {
        return $this->_getSubject()->getMagentoVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function camelize($xce)
    {
        return $this->_getSubject()->camelize($xce);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreConfig($xde, $xe2 = null)
    {
        return $this->_getSubject()->getStoreConfig($xde, $xe2);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreConfig($xf1, $xf5, $xf7 = 0)
    {
        return $this->_getSubject()->setStoreConfig($xf1, $xf5, $xf7);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreConfigUncrypted($x101)
    {
        return $this->_getSubject()->getStoreConfigUncrypted($x101);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreConfigCrypted($x10f, $x110, $x114 = 0)
    {
        return $this->_getSubject()->setStoreConfigCrypted($x10f, $x110, $x114);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConfig($x120)
    {
        return $this->_getSubject()->getDefaultConfig($x120);
    }

    /**
     * {@inheritdoc}
     */
    public function isLogEnabled()
    {
        return $this->_getSubject()->isLogEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultConfig($x12c, $x12f)
    {
        return $this->_getSubject()->setDefaultConfig($x12c, $x12f);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConfigUncrypted($x13a)
    {
        return $this->_getSubject()->getDefaultConfigUncrypted($x13a);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultConfigCrypted($x143, $x146)
    {
        return $this->_getSubject()->setDefaultConfigCrypted($x143, $x146);
    }

    /**
     * {@inheritdoc}
     */
    public function checkHeartbeat()
    {
        return $this->_getSubject()->checkHeartbeat();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastHeartbeat()
    {
        return $this->_getSubject()->getLastHeartbeat();
    }

    /**
     * {@inheritdoc}
     */
    public function dateDiff($x1a6, $x1a1 = null)
    {
        return $this->_getSubject()->dateDiff($x1a6, $x1a1);
    }

    /**
     * {@inheritdoc}
     */
    public function getDuration($x1c9)
    {
        return $this->_getSubject()->getDuration($x1c9);
    }

    /**
     * {@inheritdoc}
     */
    public function moduleIsEnabled($x1d3)
    {
        return $this->_getSubject()->moduleIsEnabled($x1d3);
    }

    /**
     * {@inheritdoc}
     */
    public function constructor($x819, $x822)
    {
        return $this->_getSubject()->constructor($x819, $x822);
    }

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return $this->_getSubject()->isAdmin();
    }

    /**
     * {@inheritdoc}
     */
    public function sendUploadResponse($x85f, $x86b, $x85d = 'application/octet-stream')
    {
        return $this->_getSubject()->sendUploadResponse($x85f, $x86b, $x85d);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($x875)
    {
        return $this->_getSubject()->notice($x875);
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        return $this->_getSubject()->isModuleOutputEnabled($moduleName);
    }
}
