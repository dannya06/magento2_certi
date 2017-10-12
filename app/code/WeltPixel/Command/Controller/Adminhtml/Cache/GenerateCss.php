<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WeltPixel\Command\Controller\Adminhtml\Cache;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class GenerateCss extends \Magento\Backend\Controller\Adminhtml\Cache
{

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \WeltPixel\Command\Model\GenerateCss
     */
    protected $generateCss;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var  ThemeProviderInterface */
    protected $themeProvider;


    /**
     * GenerateCss constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \WeltPixel\Command\Model\GenerateCss $generateCss
     * @param ScopeConfigInterface $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \WeltPixel\Command\Model\GenerateCss $generateCss,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider
    ) {
        parent::__construct($context, $cacheTypeList, $cacheState, $cacheFrontendPool, $resultPageFactory);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->generateCss = $generateCss;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
    }

    /**
     * Generate the css files from admin button trigger
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params =  $this->getRequest()->getPost();
        $storeCode = $params->get('storeview');

        if (!$storeCode) {
            $this->messageManager->addError(__('Store View is required'));
        } else {

            /** Less generation */
            $generateLessCommand = $this->_objectManager->get('WeltPixel\Command\Console\Command\GenerateLessCommand');
            $observer = $this->_objectManager->get('\Magento\Framework\Event\Observer');
            $generationContainer = $generateLessCommand->getGenerationContainer();
            $successMsg = [];
            $errorMsg = [];

            foreach ($generationContainer as $key => $item) {
                try {
                    $item->execute($observer);
                    $successMsg[] = $key . ' module less was generated successfully.';
                } catch (\Exception $ex) {
                    $errorMsg[] = $key . ' module less was not generated.';
                }
            }

            if (count($errorMsg)) {
                $this->messageManager->addError(implode("<br/>", $errorMsg));
            }

            if (count($successMsg)) {
                $this->messageManager->addSuccess(implode("<br/>", $successMsg));
            }
            /** Less generation */

            /** Css Generation  */
            try {
                $locale = $this->scopeConfig->getValue(
                    Data::XML_PATH_DEFAULT_LOCALE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeCode
                );

                $themeId = $this->scopeConfig->getValue(
                    \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeCode
                );

                $theme = $this->themeProvider->getThemeById($themeId);
                $themePath = $theme->getThemePath();

                $isPearlTheme = $this->_validatePearlTheme($theme);
                if ($isPearlTheme) {
                    $this->generateCss->processContent($themePath, $locale, $storeCode);
                    $this->messageManager->addSuccess(__('Css generation finalized.'));
                } else {
                    $this->messageManager->addNotice(__('Css generation works only for Pearl theme or subtheme.'));
                }
//                print_r($theme->getParentTheme()->getParentTheme());die;

            } catch (\Exception $ex) {
                $this->messageManager->addError($ex->getMessage());
            }
            /** Css Generation  */
        }



        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('adminhtml/*');
    }

    /**
     * @param \Magento\Theme\Model\Theme $theme
     * @return bool
     */
    protected function _validatePearlTheme($theme) {
        $pearlThemePath = 'Pearl/weltpixel';
        do {
            if ($theme->getThemePath() == $pearlThemePath) {
                return true;
            }
            $theme = $theme->getParentTheme();
        } while ($theme);

        return false;

    }
}
