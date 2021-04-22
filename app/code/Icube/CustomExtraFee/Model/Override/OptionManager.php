<?php
namespace Icube\CustomExtraFee\Model\Override;

use Amasty\Extrafee\Api\Data\FeeInterface;
use Amasty\Extrafee\Model\Config\Source\Excludeinclude;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Model\Calculation;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Amasty\Extrafee\Model\Tax;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\Fee;
use Amasty\Extrafee\Model\Rule\RuleRepository;

class OptionManager extends \Amasty\Extrafee\Model\OptionManager
{
    const HUNDRED_PERCENT = 100;

    /**
     * @var Tax
     */
    private $tax;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Rule\RuleRepository
     */
    private $ruleRepository;

    /**
     * @var Calculation
     */
    private $calculationTool;

    public function __construct(
        Tax $tax,
        PriceCurrencyInterface $priceCurrency,
        ConfigProvider $configProvider,
        Calculation $calculationTool,
        RuleRepository $ruleRepository
    ) {
        $this->tax = $tax;
        $this->priceCurrency = $priceCurrency;
        $this->configProvider = $configProvider;
        $this->calculationTool = $calculationTool;
        $this->ruleRepository = $ruleRepository;
    }

    public function fetchBaseOptions(Quote $quote, FeeInterface $fee)
    {
        $options = [];
        $storeId = $quote->getStoreId();
        $taxesEnabled = $this->configProvider->getCalcMethod() == ConfigProvider::INCLUDE_TAX;
        $baseQuoteTotal = $baseQuoteTax = $qty = null;
        $taxRate = $taxesEnabled ? $this->tax->getTaxRate($quote) : 0;
        $rate = $quote->getBaseToQuoteRate();

        foreach ($fee->getOptions() as $item) {
            $tax = $baseTax = 0.0;
            $basePrice = $this->priceToFloat($item['price']);
            $needToCalcTax = $taxesEnabled;
            if ($item['price_type'] === Fee::PRICE_TYPE_PERCENT) {
                if ($baseQuoteTotal === null) {
                    list($baseQuoteTotal, $baseQuoteTax) = $this->getBaseQuoteTotalAndBaseTax($quote, $fee);
                }
                $percent = $basePrice;
                $basePrice = $baseQuoteTotal / self::HUNDRED_PERCENT * $percent;
                if ($taxesEnabled && !$this->configProvider->useFeeTaxClassForPercentFee()) {
                    $baseTax = $baseQuoteTax / self::HUNDRED_PERCENT * $percent;
                    $tax = $this->priceCurrency->convertAndRound($baseTax);
                    $needToCalcTax = false;
                }
            } elseif ($item['price_type'] === Fee::PRICE_TYPE_FIXED) {
                if (!$qty) {
                    $qty = 1;
                    if ($fee->isPerProduct()) {
                        $qty = $this->getValidQty($quote, $fee);
                    }
                }
                $basePrice *= $qty;
            }

            $price = $this->priceCurrency->convertAndRound($basePrice);

            if($fee->getName() == 'Unique Code Bank Transfer') {
                $unicode = mt_rand(1,500);    
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->create('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $sql = "Select * FROM amasty_extrafee_quote where quote_id = ".$quote->getId()." and option_id = ".$item['entity_id'];
                $result = $connection->fetchRow($sql);
                $feeId = null;
                $feeAmount = null;
                if (!empty($result)) {
                    $feeId = !empty($result['fee_id']) ? (int) $result['fee_id'] : false;
                    $feeAmount = !empty($result['fee_amount']) ? (int) $result['fee_amount'] : false;
                }

                if ($feeId && $feeAmount>0) {
                    $basePrice = $feeAmount;
                }else{
                    $basePrice = $unicode;  
                }
                $price = $basePrice * $rate;
            }

            if ($needToCalcTax && $taxRate) {
                $baseTax = $this->calculationTool->calcTaxAmount(
                    $basePrice,
                    $taxRate,
                    false,
                    false
                );

                $tax = $this->calculationTool->calcTaxAmount(
                    $price,
                    $taxRate,
                    false,
                    false
                );
            }

            $options[] = [
                'index' => $item['entity_id'],
                'price' => $price,
                'base_price' => $this->priceCurrency->round($basePrice),
                'tax' => $tax,
                'base_tax' => $baseTax,
                'default' => $item['default'],
                'value_incl_tax' => $price + $tax,
                'value_excl_tax' => $price,
                'label' => $this->getOptionLabel($storeId, $item['options'])
            ];
        }
        return $options;
    }

    private function priceToFloat($price)
    {
        // convert "," to "."
        $price = str_replace(',', '.', $price);
        // remove everything except numbers and dot "."
        $price = preg_replace("/[^0-9\.]/", "", $price);
        // remove all seperators from first part and keep the end
        $price = str_replace('.', '', substr($price, 0, -3)) . substr($price, -3);

        return (float)$price;
    }
}
