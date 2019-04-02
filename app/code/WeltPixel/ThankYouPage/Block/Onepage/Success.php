<?php

namespace WeltPixel\ThankYouPage\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $renderer;

    /**
     * @var
     */
    protected $string;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    private $productRepository;

    /**
     * Success constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Sales\Model\Order\Address\Renderer $renderer
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\Order\Address\Renderer $renderer,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->renderer = $renderer;
        $this->string = $string;
        $this->imageBuilder = $imageBuilder;
        $this->productRepository = $productRepository;

        parent::__construct(
            $context, $checkoutSession, $orderConfig, $httpContext, $data
        );
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getLastOrder() {
        return $this->_checkoutSession->getLastRealOrder();
    }

    /**
     * @param $address
     * @return null|string
     */
    public function getFormattedAddress($address) {
        return $this->renderer->format($address, 'html');
    }

    /**
     * @param $address
     * @param $fields
     * @return string
     */
    public function addressToString($address, $fields)
    {
        $string = '';
        foreach ($fields as $code) {
            $string .= $address->getData($code) . ', ';
        }

        return trim($string, ', ');
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getPaymentMethodTitle($order) {
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();

        return $method->getTitle();
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getShippingMethodTitle($order) {
        return $order->getShippingDescription();
    }

    /**
     * Prepare SKU
     * @param $sku
     * @return string
     */
    public function prepareSku($sku)
    {
        return $this->escapeHtml($this->string->splitInjection($sku));
    }

    /**
     * Return item unit price html
     * @param null $item
     * @return string
     */
    public function getItemPriceHtml($item = null)
    {
        $block = $this->getLayout()->getBlock('item_unit_price');
        if (!$item) {
            $item = $this->getItem();
        }
        $block->setItem($item);
        return $block->toHtml();
    }

    /**
     * Return item row total html
     * @param null $item
     * @return string
     */
    public function getItemRowTotalHtml($item = null)
    {
        $block = $this->getLayout()->getBlock('item_row_total');
        if (!$item) {
            $item = $this->getItem();
        }
        $block->setItem($item);
        return $block->toHtml();
    }

    /**
     * Return selected simple product if $_item has options
     *
     * @param $_item
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProductFromItem($_item)
    {
        $options = $_item->getProductOptions();
        if (isset($options['simple_sku'])) {
            return $this->productRepository->get($options['simple_sku']);
        }

        return $_item->getProduct();
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
}