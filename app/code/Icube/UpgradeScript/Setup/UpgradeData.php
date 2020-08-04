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
        PageFactory $pageFactory
    ) {
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
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->howtopayblock();
            $this->howtopaypages();
        }
    }

    /* CMS for Return Exchange */

    function returnexchange()
    {
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

    public function howtopayblock()
    {
        $cmsBlockContent = <<<EOD
        <div class="accordion accordion-bg clearfix" data-bind="mageInit: {'accordion':{'openedState': '_active'}} ">
<div data-role="collapsible">
<div class="acctitle" data-role="title">Bank Transfer Payment</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Bank Transfer</div>
<div class="acc_content clearfix" data-role="content">
<h3>BCA</h3>
<h5>ATM BCA</h5>
1. On the main menu, choose Other Transaction.<br>2. Choose Transfer.<br>3. Choose Transfer To BCA Virtual Account.<br>4. Enter your Payment Code (11 digits code) and press Correct.<br>5. Enter the full amount to be paid and press Correct.<br>6. Your payment details will appear on the payment confirmation page. If the information is correct press Yes.<br>
<h5>Klik BCA</h5>
1. Choose Menu Fund Transfer.<br>2. Choose Transfer To BCA Virtual Account.<br>3. Input BCA Virtual Account Number or Choose from Transfer list and click Continue.<br>4. Amount to be paid, account number and Merchant name will appear on the payment confirmation page, if the information is right click Continue.<br>5. Get your BCA token and input KEYBCA Response APPLI 1 and click Submit.<br>6. Your Transaction is Done.<br>
<h5>m-BCA</h5>
1. Log in to your BCA Mobile app.<br>2. Choose m-BCA, then input your m-BCA access code.<br>3. Choose m-Transfer, then choose BCA Virtual Account.<br>4. Input Virtual Account Number or choose an existing account from Daftar Transfer.<br>5. Input the payable amount.<br>6. Input your m-BCA pin.<br>7. Payment is finished. Save the notification as your payment receipt.<br>
<h3>Mandiri</h3>
<h5>ATM Mandiri</h5>
1. On the main menu, choose Pay/Buy.<br>2. Choose Others.<br>3. Choose Multi Payment.<br>4. Enter 70012 (Midtrans company code) and press Correct.<br>5. Enter your Payment Code and press Correct.<br>6. Your payment details will appear on the payment confirmation page. If the information is correct press Yes.<br>
<h5>Internet Banking</h5>
1. Login to Mandiri Internet Banking (https://ib.bankmandiri.co.id/).<br>2. From the main menu choose Payment, then choose Multi Payment.<br>3. Select your account in From Account, then in Billing Name select Midtrans.<br>4. Enter the Payment Code and you will receive your payment details.<br>5. Confirm your payment using your Mandiri Token.<br>
<h3>BNI</h3>
<h5>ATM BNI</h5>
1. On the main menu, choose Others.<br>2. Choose Transfer.<br>3. Choose Savings Account.<br>4. Choose To BNI Account.<br>5. Enter the payment account number and press Yes.<br>6. Enter the full amount to be paid. If the amount entered is not the same as the invoiced amount, the transaction will be declined.<br>7. Amount to be paid, account number, and merchant name will appear on the payment confirmation page. If the information is correct, press Yes.<br>8. You are done.<br>
<h5>Internet Banking</h5>
1. Go to https://ibank.bni.co.id and then click Login.<br>2. Continue login with your User ID and Password.<br>3. Click Transfer and then Add Favorite Account and choose Antar Rekening BNI.<br>4. Enter account name, account number, and email and then click Continue.<br>5. Input the Authentification Code from your token and then click Continue.<br>6. Back to main menu and select Transfer and then Transfer Antar Rekening BNI.<br>7. Pick the account that you just created in the previous step as Rekening Tujuan and fill in the rest before clicking Continue.<br>8. Check whether the details are correct, if they are, please input the Authentification Code and click Continue and you are done.<br>
<h5>Mobile Banking</h5>
1. Open the BNI Mobile Banking app and login<br>2. Choose menu Transfer<br>3. Choose menu Virtual Account Billing<br>4. Choose the bank account you want to use<br>5. Enter the 16 digits virtual account number<br>6. The billing information will appear on the payment validation page<br>7. If the information is correct, enter your password to proceed the payment<br>8. Your transaction will be processed<br>
<h3>Permata</h3>
1. On the main menu, choose Other Transaction.<br>2. Choose Payment.<br>3. Choose Other Payment.<br>4. Choose Virtual Account.<br>5. Enter 16 digits Account No. and press Correct.<br>6. Amount to be paid, account number, and merchant name will appear on the payment confirmation page. If the information is right, press Correct.<br>7. Choose your payment account and press Correct.<br>
<h3>ATM Network</h3>
<h5>Prima</h5>
1. On the main menu, choose Other Transaction.<br>2. Choose Transfer.<br>3. Choose Other Bank Account.<br>4. Enter 009 (Bank BNI code) and choose Correct.<br>5. Enter the full amount to be paid. If the amount entered is not the same as the invoiced amount, the transaction will be declined.<br>6. Enter 16 digits payment Account No. and press Correct.<br>7. Amount to be paid, account number, and merchant name will appear on the payment confirmation page. If the information is right, press Correct.<br>
<h5>ATM Bersama</h5>
1. On the main menu, choose Others.<br>2. Choose Transfer.<br>3. Choose Online Transfer.<br>4. Enter 009 (Bank BNI code) and 16 digits Account No. and press Correct.<br>5. Enter the full amount to be paid. If the amount entered is not the same as the invoiced amount, the transaction will be declined.<br>6. Empty the transfer reference number and press Correct.<br>7. Amount to be paid, account number, and merchant name will appear on the payment confirmation page. If the information is right, press Correct.<br>
<h5>Alto</h5>
1. On the main menu, choose Other Transaction.<br>2. Choose Transfer.<br>3. Choose Other Bank Account.<br>4. Enter 009 (Bank BNI code) and choose Correct.<br>5. Enter the full amount to be paid. If the amount entered is not the same as the invoiced amount, the transaction will be declined.<br>6. Enter 16 digits payment Account No. and press Correct.<br>7. Amount to be paid, account number, and merchant name will appear on the payment confirmation page. If the information is right, press Correct.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap BCA Virtual Account</div>
<div class="acc_content clearfix" data-role="content">
<h5>ATM BCA</h5>
1. On the main menu, choose Other Transaction.<br>2. Choose Transfer.<br>3. Choose Transfer To BCA Virtual Account.<br>4. Enter your Payment Code (11 digits code) and press Correct.<br>5. Enter the full amount to be paid and press Correct.<br>6. Your payment details will appear on the payment confirmation page. If the information is correct press Yes.<br>
<h5>Klik BCA</h5>
1. Choose Menu Fund Transfer.<br>2. Choose Transfer To BCA Virtual Account.<br>3. Input BCA Virtual Account Number or Choose from Transfer list and click Continue.<br>4. Amount to be paid, account number and Merchant name will appear on the payment confirmation page, if the information is right click Continue.<br>5. Get your BCA token and input KEYBCA Response APPLI 1 and click Submit.<br>6. Your Transaction is Done.<br>
<h5>m-BCA</h5>
1. Log in to your BCA Mobile app.<br>2. Choose m-BCA, then input your m-BCA access code.<br>3. Choose m-Transfer, then choose BCA Virtual Account.<br>4. Input Virtual Account Number or choose an existing account from Daftar Transfer.<br>5. Input the payable amount.<br>6. Input your m-BCA pin.<br>7. Payment is finished. Save the notification as your payment receipt.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap BNI VA</div>
<div class="acc_content clearfix" data-role="content">
<h5>ATM BNI</h5>
1. On the main menu, choose Others.<br>2. Choose Transfer.<br>3. Choose Savings Account.<br>4. Choose To BNI Account.<br>5. Enter the payment account number and press Yes.<br>6. Enter the full amount to be paid. If the amount entered is not the same as the invoiced amount, the transaction will be declined.<br>7. Amount to be paid, account number, and merchant name will appear on the payment confirmation page. If the information is correct, press Yes.<br>8. You are done.<br>
<h5>Internet Banking</h5>
1. Go to https://ibank.bni.co.id and then click Login.<br>2. Continue login with your User ID and Password.<br>3. Click Transfer and then Add Favorite Account and choose Antar Rekening BNI.<br>4. Enter account name, account number, and email and then click Continue.<br>5. Input the Authentification Code from your token and then click Continue.<br>6. Back to main menu and select Transfer and then Transfer Antar Rekening BNI.<br>7. Pick the account that you just created in the previous step as Rekening Tujuan and fill in the rest before clicking Continue.<br>8. Check whether the details are correct, if they are, please input the Authentification Code and click Continue and you are done.<br>
<h5>Mobile Banking</h5>
1. Open the BNI Mobile Banking app and login<br>2. Choose menu Transfer<br>3. Choose menu Virtual Account Billing<br>4. Choose the bank account you want to use<br>5. Enter the 16 digits virtual account number<br>6. The billing information will appear on the payment validation page<br>7. If the information is correct, enter your password to proceed the payment<br>8. Your transaction will be processed</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Permata VA</div>
<div class="acc_content clearfix" data-role="content">1. On the main menu, choose Other Transaction.<br>2. Choose Payment.<br>3. Choose Other Payment.<br>4. Choose Virtual Account.<br>5. Enter 16 digits Account No. and press Correct.<br>6. Amount to be paid, account number, and merchant name will appear on the payment confirmation page. If the information is right, press Correct.<br>7. Choose your payment account and press Correct.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Mandiri Installment</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap BCA Installment</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap MayBank Installment</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Credit Card</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Credit Card With BIN</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap GO-Pay</div>
<div class="acc_content clearfix" data-role="content">1. Open your preferred QRIS-supporting payment app. <br>2. Scan the QR code shown on your monitor.<br>3. Check your payment details in the app, then tap Pay.<br>4. Your transaction is complete.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Klik BCA</div>
<div class="acc_content clearfix" data-role="content">1. Visit KlikBCA website www.klikbca.com.<br>2. Login using your KlikBCA’s userID.<br>3. Choose e-commerce Payment Menu.<br>4. Choose Category Others.<br>5. Choose Company Name.<br>6. Click Continue.<br>7. Choose the transaction that you want to pay and choose continue.<br>8. Re-confirm the payment by inputting the token key and choose submit/continue.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap CIMB Click</div>
<div class="acc_content clearfix" data-role="content">1. Please make sure that you have a User ID for CIMB Clicks and have registered your mPIN before going through with the payment.<br>2. Payment via CIMB Clicks will be processed online and your CIMB bank account balance will be deducted automatically based on your total amount.<br>3. Transaction will be cancelled if payment is not completed within 2 hours.<br>4. Please make sure that there is no pop-up blocker on your browser.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap BCA KlikPay</div>
<div class="acc_content clearfix" data-role="content">1. You will be redirected to BCA KlikPay page once you click "Pay Now" button.<br>2. After Login to your BCA Klikpay Account by entering your email address and password, it will display transaction information such as merchant name, transaction time, and amount to be paid. Choose the type of payment KlikBCA or BCA Card for the transaction.<br>3. To authorize payment with BCA KlikPay, press the "send OTP" button, and you will receive an OTP (One Time Password) code sent via SMS to your mobile phone. Enter the OTP code in the fields provided.<br>4. If your OTP code is correct, your payment will be processed immediately and your account balance (for KlikBCA payment type) or your BCA Card limit (for BCA Card payment type) will be reduced according to amount of transaction value.<br>5. Your transaction success status will appear on the transaction screen and you will receive a notification email.<br>6. For more information about BCA KlikPay please contact Halo BCA at 1500888 or visit http://klikbca.com/KlikPay/klikpay.html</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Indomaret</div>
<div class="acc_content clearfix" data-role="content">1. After confirming your payment, we will issue you a unique Payment Code number.<br>2. Note down your Payment Code and total amount. Don't worry, we will also mail you a copy of this payment instructions to your email.<br>3. Go to an Indomaret store near you and provide the cashier with the Payment Code number.<br>4. The cashier will then confirm the transaction by asking for the transaction amount and the merchant name.<br>5. Confirm the payment with the cashier.<br>6. Your transaction is successful! You should be receiving an email confirming your payment. Please keep your Indomaret payment receipt just in case you need help via support.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Alfamart</div>
<div class="acc_content clearfix" data-role="content">1. After confirming your payment, we will issue you a unique Payment Code number.<br>2. Note down your Payment Code and total amount. Don't worry, we will also mail you a copy of this payment instructions to your email. <br>3. Go to an Alfamart, Alfamidi, or Dan+Dan store near you and provide the cashier with the Payment Code number.<br>4. The cashier will then confirm the transaction by asking for the transaction amount and the merchant name.<br>5. Confirm the payment with the cashier to proceed payment.<br>6. Your transaction is successful! You should be receiving an email confirming your payment. Please keep your payment receipt just in case you need help via support.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Snap Mandiri Bill Payment (echannel)</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
<div data-role="collapsible">
<div class="acctitle" data-role="title">Cash On Delivery</div>
<div class="acc_content clearfix" data-role="content">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
</div>
</div>
EOD;
        $cmsBlock = $this->createBlock()->load('how-to-pay', 'identifier');
        $cmsBlockData = [
            'title' => 'How to Pay',
            'identifier' => 'how-to-pay',
            'content' => $cmsBlockContent,
            'is_active' => 1,
            'stores' => 0
        ];
        if (!$cmsBlock->getId()) {
            $this->createBlock()->setData($cmsBlockData)->save();
        } else {
            $cmsBlock
            ->setTitle($cmsBlockData['title'])
            ->setContent($cmsBlockContent)
            ->setIsActive($cmsBlockData['is_active'])
            ->setStores($cmsBlockData['stores'])
            ->save();
        }
    }

    function howtopaypages()
    {
        $pageContent = <<<EOD
        <p>{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="how-to-pay"}}</p>
EOD;

        $cmsPage = $this->createPage()->load('how-to-pay', 'identifier');

        if (!$cmsPage->getId()) {
            $cmsPageContent = [
                'title' => 'How to Pay',
                'content_heading' => '',
                'page_layout' => '1column',
                'identifier' => 'how-to-pay',
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
}