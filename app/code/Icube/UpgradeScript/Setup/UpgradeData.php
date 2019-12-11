<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\UpgradeScript\Setup;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory,
        PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * Create page
     *
     * @return Page
     */
    public function createPage()
    {
        return $this->pageFactory->create();
    }

    /**
     * Create block
     *
     * @return Page
     */
    public function createBlock()
    {
        return $this->blockFactory->create();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->returnexchange();
            $this->weltpixel_footer_v1();
            $this->weltpixel_footer_v2();
            $this->weltpixel_footer_v3();
            $this->weltpixel_footer_v4();
        }
        
	}

    /* CMS for Return Exchange */

    function returnexchange() {
        $pageContent = <<<EOD
        <p>Returns without the original receipt must reference the order number, which you may reprint from your order confirmation email. For customer convenience all orders are shipped with a prepaid JNE shipping label.</p>
        <h2>RETURNS BY JNE:</h2>
        <ol>
        <li>Keep the order summary portion of your shipping form as well as your order number and JNE tracking number from the prepaid JNE shipping label.</li>
        <li>On the Return Form, note the reason for the return: e.g. wrong item shipped; wrong color, etc. and include the form in the box.</li>
        <li>Use the pre-addressed prepaid JNE shipping label and drop the package off at any JNE location or drop box.</li>
        </ol>
        <h2>WHAT IF I LOST MY RETURN SLIP ?</h2>
        <p>If you do not have the mailing label from the original packing slip please use a signature-required service such as JNE to send your return back to us, or you can print the your data payment which we have sent to you by e-mail.<br><br>Mail your package to:<br><strong>New York, NY, 00841</strong><br>1-800-000-0000<br>yourmail@yourdomain.com</p>
        <h2>WHAT IF I DON’T HAVE MY ORDER NUMBER ?</h2>
        <p>If you don’t have your order number, please contact Customer Service so we can help you to get the order number. Please contact them via email at yourmail@yourdomain.com or call them at 1-800-000-0000.</p>
        <h2>SHIPPING DAMAGE:</h2>
        <p>If you receive an item that was damaged during shipment, contact our Customer Service team within 10 days of delivery at 1-800-000-0000. Please have your order number, item number and tracking number from your original confirmation e-mail.</p>
        <h2>GUARANTEED FOR LIFE</h2>
        <p>Quality. Durability. Reliability.<br> So if your pack ever breaks down, simply return it to our warranty center. We’ll fix it and if we can’t we’ll replace it*.</p>
        <h2>CUSTOMER SERVICE</h2>
        <p>We want to make sure you're happy with your shopping experience. Our Customer Service team can help resolve any problems you may have experienced with your purchase. Please contact them via email at yourmail@yourdomain.com or call them at 1-800-000-0000.</p>
        <p><br>*terms and conditions apply.</p>
EOD;

        $cmsPage = $this->createPage()->load('return-exchange', 'identifier');

        if (!$cmsPage->getId()) {
            $cmsPageContent = [
                'title' => 'Return & Exchange',
                'content_heading' => '',
                'page_layout' => '1column',
                'identifier' => 'return-exchange',
                'content' => $pageContent,
                'is_active' => 1,
                'stores' => [1],
                'sort_order' => 0,
            ];
            $this->createPage()->setData($cmsPageContent)->save();
        } else {
            $cmsPage->setContent($pageContent)->save();
        }
    }

    /* End of Return Exchange */

    /* WeltPixel Footer V1 */
    function weltpixel_footer_v1() {
        $cmsBlockContent = <<<EOD
        <div class="footer-v1">
<div class="footer-v1-content">
<div class="col-sm-3 col-xs-12 nopaddingleft">
<h4 class="mini-logo"><img style="width: 100px;" src="{{media url="wysiwyg/pearl_theme/pearl_logo.png"}}" alt="Logo"></h4>
<p>New York, NY, 00841</p>
<p>1-800-000-0000</p>
<p>yourmail@yourdomain.com</p>
</div>
<div class="col-sm-3 col-xs-12 nopaddingleft">
<p class="footer-title">Pearl Theme</p>
<ul class="footer links">
<li class="nav item"><a href="https://www.weltpixel.com/magento-2-theme-pearl/" target="_blank" rel="noopener">Buy Pearl Theme</a></li>
<li class="nav item"><a href="https://www.weltpixel.com/resources/PearlTheme/User-Guide-WeltPixel-Pearl-Theme-Magento2.html" target="_blank" rel="noopener">User Guide</a></li>
<li class="nav item"><a href="http://pearl.weltpixel.com/pearl.weltpixel.com/admin/demologin" target="_blank" rel="noopener">Demo Admin</a></li>
<li class="nav item"><a href="https://support.weltpixel.com/hc/en-us" target="_blank" rel="noopener">Help Center</a></li>
</ul>
</div>
<div class="col-sm-3 col-xs-12 nopaddingleft">
<p class="footer-title">Store</p>
<ul class="footer links">
<li class="nav item"><a href="{{store url="customer/account/login/"}}">Sign In</a></li>
<li class="nav item"><a href="{{store url="sales/guest/form/"}}">Orders and Returns</a></li>
<li class="nav item"><a href="{{store url="privacy-policy-cookie-restriction-mode"}}">Privacy Policy</a></li>
<li class="nav item"><a href="{{store url="contact/"}}">Contact Us</a></li>
<li class="nav item"><a href="{{store url="return-exchange"}}">Return & Exchange</a></li>
</ul>
</div>
<div class="col-sm-3 col-xs-12 nopaddingleft">
<p class="footer-title">Search</p>
<ul class="footer links">
<li class="nav item"><a href="{{store url="catalogsearch/advanced/"}}">Search Terms</a></li>
<li class="nav item"><a href="{{store url="catalogsearch/advanced/"}}">Advanced Search</a></li>
</ul>
</div>
<div class="col-xs-12 border-v1">
<div class="pull-left-md">
<p class="small-text">Let's stay in touch!</p>
</div>
<div class="pull-right-md social-icons-v1">&nbsp;</div>
</div>
</div>
</div>
EOD;
        $cmsBlock = $this->createBlock()->load('weltpixel_footer_v1', 'identifier');

        $cmsBlockData = [
            'title' => 'WeltPixel Footer V1',
            'identifier' => 'weltpixel_footer_v1',
            'content' => $cmsBlockContent,
            'is_active' => 1,
            'stores' => 1
        ];
        if (!$cmsBlock->getId()) {
            $this->createBlock()->setData($cmsBlockData)->save();
        } else {
            $cmsBlock
            ->setTitle($cmsBlockData['title'])
            ->setContent($cmsBlockContent)
            ->setIsActive($cmsBlockData['is_active'])
            ->save();
        }
    }
    /* end WeltPixel Footer v1*/

    /* WeltPixel Footer V2 */
    function weltpixel_footer_v2() {
        $cmsBlockContent = <<<EOD
        <div class="footer-v2">
<div class="center">
<div class="clearfix">&nbsp;</div>
<div class="footer-nav"><a>FAQ</a> <a>Privacy</a> <a>Terms of Use</a> <a>Contact</a> <a href="{{store url="return-exchange/"}}">Return & Exchange</a></div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="toggle center">
<div class="togglet toggleta newsletter" style="text-decoration: underline;">Subscribe to the Newsletter</div>
<div class="togglec nopadding-left-mob">
<div class="block newsletter">
<div class="title"><strong>Newsletter</strong></div>
<div class="content"><form id="newsletter-validate-detail" class="form subscribe hp-newsletter-v1" action="{{store url="newsletter/subscriber/new/"}}" method="post" novalidate="novalidate" data-mage-init="{"validation": {"errorClass": "mage-error"}}">
<div class="field newsletter">
<div class="control"><input id="newsletter-bottom" name="email" type="email" placeholder="Enter your email address" data-validate="{required:true, 'validate-email':true}"></div>
</div>
<div class="actions"><button class="action subscribe primary" title="Subscribe" type="submit"> Subscribe </button></div>
</form></div>
</div>
</div>
</div>
</div>
EOD;
        $cmsBlock = $this->createBlock()->load('weltpixel_footer_v2', 'identifier');

        $cmsBlockData = [
            'title' => 'WeltPixel Footer V2',
            'identifier' => 'weltpixel_footer_v2',
            'content' => $cmsBlockContent,
            'is_active' => 1,
            'stores' => 1
        ];
        if (!$cmsBlock->getId()) {
            $this->createBlock()->setData($cmsBlockData)->save();
        } else {
            $cmsBlock
            ->setTitle($cmsBlockData['title'])
            ->setContent($cmsBlockContent)
            ->setIsActive($cmsBlockData['is_active'])
            ->save();
        }
    }
    /* end WeltPixel Footer v2*/
    
    /* WeltPixel Footer V3 */
    function weltpixel_footer_v3() {
        $cmsBlockContent = <<<EOD
        <div class="w footer-v3">
<div class="footer-section2-content row"><!-- FOOTER COLUMN #1 BEGIN -->
<div class="col-md-3 col-sm-6 col-xs-12 mobile-toggle address-v3">
<h4 class="mini-logo"><img style="width: 100px;" src="{{media url=" alt="Logo"></h4>
<p class="details-v3">9087S Divamus Faucibus Str., <br> City name, Postal Code, <br> PA 19130, United States. <br><br> (1800) 000 000<br> contact@domain.com</p>
</div>
<!-- FOOTER COLUMN #1 BEGIN --> <!-- FOOTER COLUMN #2 BEGIN -->
<div class="col-md-3 col-sm-6 col-xs-12 mobile-toggle">
<h4 class="no-padding-mob">Company</h4>
<ul class="footer-v3-list">
<li><a href="{{store url="about-us/"}}">About Us</a></li>
<li><a href="{{store url="contact/"}}">Contact Us</a></li>
<li><a href="{{store url="customer-service/"}}">Customer Service</a></li>
<li><a href="{{store url="privacy-policy-cookie-restriction-mode/"}}">Privacy Policy</a></li>
<li><a href="{{store url="return-exchange/"}}">Return & Exchange</a></li>
</ul>
</div>
<!-- FOOTER COLUMN #2 END -->
<div class="clearfix visible-sm-block">&nbsp;</div>
<!-- FOOTER COLUMN #3 BEGIN -->
<div class="col-md-3 col-sm-6 col-xs-12 mobile-toggle">
<h4 class="no-padding-mob">Quick Links</h4>
<ul class="footer-v3-list">
<li><a title="Site Map" href="{{store url="sitemap.xml"}}">Site Map</a></li>
<li><a title="Search Terms" href="{{store url="search/term/popular/"}}">Search Terms</a></li>
<li><a title="Advanced Search" href="{{store url="catalogsearch/advanced/"}}">Advanced Search</a></li>
<li><a title="Documentation" href="https://www.weltpixel.com/resources/PearlTheme/User-Guide-WeltPixel-Pearl-Theme-Magento2.html" target="_blank" rel="noopener">Documentation</a></li>
</ul>
</div>
<!-- FOOTER COLUMN #3 END --> <!-- FOOTER COLUMN #4 BEGIN -->
<div class="col-md-3 col-sm-6 col-xs-12 mobile-toggle">
<h4 class="no-padding-mob">Let's Stay in touch!</h4>
<div class="mg-mobile"><!-- FOOTER SOCIAL-ICONS BEGIN --> <!-- FOOTER SOCIAL-ICONS END --> <!-- FOOTER NEWSLETTER BLOCK BEGIN -->
<div class="newsletter-subscribe"><form id="newsletter-footer" action="{{store url=" method="post" data-mage-init="{"validation": {"errorClass": "mage-error"}}">
<div class="form-group"><input id="newsletter-bottom" class="input-text required-entry validate-email" name="email" type="email" placeholder="Enter your email address" data-validate="{required:true, 'validate-email':true}"> <button class="button" title="Subscribe" type="submit"> Sign Up </button></div>
</form></div>
<!-- FOOTER NEWSLETTER BLOCK END --></div>
</div>
<!-- FOOTER COLUMN #4 END --></div>
</div>
EOD;
        $cmsBlock = $this->createBlock()->load('weltpixel_footer_v3', 'identifier');

        $cmsBlockData = [
            'title' => 'WeltPixel Footer V3',
            'identifier' => 'weltpixel_footer_v3',
            'content' => $cmsBlockContent,
            'is_active' => 1,
            'stores' => 1
        ];
        if (!$cmsBlock->getId()) {
            $this->createBlock()->setData($cmsBlockData)->save();
        } else {
            $cmsBlock
            ->setTitle($cmsBlockData['title'])
            ->setContent($cmsBlockContent)
            ->setIsActive($cmsBlockData['is_active'])
            ->save();
        }
    }
    /* end WeltPixel Footer v3*/
    
    /* WeltPixel Footer V4 */
    function weltpixel_footer_v4() {
        $cmsBlockContent = <<<EOD
        <div class="footer-v4">
<div class="center">
<div class="clearfix">&nbsp;</div>
<div class="footer-nav"><a>FAQ</a> <a>Privacy</a> <a>Terms of Use</a> <a>Contact</a> <a href="{{store url="return-exchange/"}}">Return & Exchange</a></div>
</div>
<div class="clearfix">&nbsp;</div>
<div class="toggle center">
<div class="togglet toggleta newsletter" style="text-decoration: underline;">Subscribe to the Newsletter</div>
<div class="togglec nopadding-left-mob">
<div class="block newsletter">
<div class="title"><strong>Newsletter</strong></div>
<div class="content"><form id="newsletter-validate-detail" class="form subscribe hp-newsletter-v1" action="{{store url="newsletter/subscriber/new/"}}" method="post" novalidate="novalidate" data-mage-init="{"validation": {"errorClass": "mage-error"}}">
<div class="field newsletter">
<div class="control"><input id="newsletter-bottom" name="email" type="email" placeholder="Enter your email address" data-validate="{required:true, 'validate-email':true}"></div>
</div>
<div class="actions"><button class="action subscribe primary" title="Subscribe" type="submit"> Subscribe </button></div>
</form></div>
</div>
</div>
</div>
</div>
EOD;
        $cmsBlock = $this->createBlock()->load('weltpixel_footer_v4', 'identifier');

        $cmsBlockData = [
            'title' => 'WeltPixel Footer V4',
            'identifier' => 'weltpixel_footer_v4',
            'content' => $cmsBlockContent,
            'is_active' => 1,
            'stores' => 1
        ];
        if (!$cmsBlock->getId()) {
            $this->createBlock()->setData($cmsBlockData)->save();
        } else {
            $cmsBlock
            ->setTitle($cmsBlockData['title'])
            ->setContent($cmsBlockContent)
            ->setIsActive($cmsBlockData['is_active'])
            ->save();
        }
    }
    /* end WeltPixel Footer v4*/
}
