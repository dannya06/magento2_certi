<?php
namespace Magento\Sales\Api\Data;

/**
 * Extension class for @see \Magento\Sales\Api\Data\OrderInterface
 */
class OrderExtension extends \Magento\Framework\Api\AbstractSimpleObject implements OrderExtensionInterface
{
    /**
     * @return \Magento\Sales\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments()
    {
        return $this->_get('shipping_assignments');
    }

    /**
     * @param \Magento\Sales\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->setData('shipping_assignments', $shippingAssignments);
        return $this;
    }

    /**
     * @return \Amasty\Orderattr\Api\Data\OrderAttributeDataInterface[]|null
     */
    public function getAmastyOrderAttributes()
    {
        return $this->_get('amasty_order_attributes');
    }

    /**
     * @param \Amasty\Orderattr\Api\Data\OrderAttributeDataInterface[] $amastyOrderAttributes
     * @return $this
     */
    public function setAmastyOrderAttributes($amastyOrderAttributes)
    {
        $this->setData('amasty_order_attributes', $amastyOrderAttributes);
        return $this;
    }

    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getGiftMessage()
    {
        return $this->_get('gift_message');
    }

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage
     * @return $this
     */
    public function setGiftMessage(\Magento\GiftMessage\Api\Data\MessageInterface $giftMessage)
    {
        $this->setData('gift_message', $giftMessage);
        return $this;
    }

    /**
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[]|null
     */
    public function getAppliedTaxes()
    {
        return $this->_get('applied_taxes');
    }

    /**
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[] $appliedTaxes
     * @return $this
     */
    public function setAppliedTaxes($appliedTaxes)
    {
        $this->setData('applied_taxes', $appliedTaxes);
        return $this;
    }

    /**
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[]|null
     */
    public function getItemAppliedTaxes()
    {
        return $this->_get('item_applied_taxes');
    }

    /**
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[] $itemAppliedTaxes
     * @return $this
     */
    public function setItemAppliedTaxes($itemAppliedTaxes)
    {
        $this->setData('item_applied_taxes', $itemAppliedTaxes);
        return $this;
    }

    /**
     * @return boolean|null
     */
    public function getConvertingFromQuote()
    {
        return $this->_get('converting_from_quote');
    }

    /**
     * @param boolean $convertingFromQuote
     * @return $this
     */
    public function setConvertingFromQuote($convertingFromQuote)
    {
        $this->setData('converting_from_quote', $convertingFromQuote);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAwUseRewardPoints()
    {
        return $this->_get('aw_use_reward_points');
    }

    /**
     * @param int $awUseRewardPoints
     * @return $this
     */
    public function setAwUseRewardPoints($awUseRewardPoints)
    {
        $this->setData('aw_use_reward_points', $awUseRewardPoints);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwRewardPointsAmount()
    {
        return $this->_get('aw_reward_points_amount');
    }

    /**
     * @param float $awRewardPointsAmount
     * @return $this
     */
    public function setAwRewardPointsAmount($awRewardPointsAmount)
    {
        $this->setData('aw_reward_points_amount', $awRewardPointsAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwRewardPointsAmount()
    {
        return $this->_get('base_aw_reward_points_amount');
    }

    /**
     * @param float $baseAwRewardPointsAmount
     * @return $this
     */
    public function setBaseAwRewardPointsAmount($baseAwRewardPointsAmount)
    {
        $this->setData('base_aw_reward_points_amount', $baseAwRewardPointsAmount);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAwRewardPoints()
    {
        return $this->_get('aw_reward_points');
    }

    /**
     * @param int $awRewardPoints
     * @return $this
     */
    public function setAwRewardPoints($awRewardPoints)
    {
        $this->setData('aw_reward_points', $awRewardPoints);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwRewardPointsShippingAmount()
    {
        return $this->_get('aw_reward_points_shipping_amount');
    }

    /**
     * @param float $awRewardPointsShippingAmount
     * @return $this
     */
    public function setAwRewardPointsShippingAmount($awRewardPointsShippingAmount)
    {
        $this->setData('aw_reward_points_shipping_amount', $awRewardPointsShippingAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwRewardPointsShippingAmount()
    {
        return $this->_get('base_aw_reward_points_shipping_amount');
    }

    /**
     * @param float $baseAwRewardPointsShippingAmount
     * @return $this
     */
    public function setBaseAwRewardPointsShippingAmount($baseAwRewardPointsShippingAmount)
    {
        $this->setData('base_aw_reward_points_shipping_amount', $baseAwRewardPointsShippingAmount);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAwRewardPointsShipping()
    {
        return $this->_get('aw_reward_points_shipping');
    }

    /**
     * @param int $awRewardPointsShipping
     * @return $this
     */
    public function setAwRewardPointsShipping($awRewardPointsShipping)
    {
        $this->setData('aw_reward_points_shipping', $awRewardPointsShipping);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAwRewardPointsDescription()
    {
        return $this->_get('aw_reward_points_description');
    }

    /**
     * @param string $awRewardPointsDescription
     * @return $this
     */
    public function setAwRewardPointsDescription($awRewardPointsDescription)
    {
        $this->setData('aw_reward_points_description', $awRewardPointsDescription);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwRewardPointsInvoiced()
    {
        return $this->_get('base_aw_reward_points_invoiced');
    }

    /**
     * @param float $baseAwRewardPointsInvoiced
     * @return $this
     */
    public function setBaseAwRewardPointsInvoiced($baseAwRewardPointsInvoiced)
    {
        $this->setData('base_aw_reward_points_invoiced', $baseAwRewardPointsInvoiced);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwRewardPointsInvoiced()
    {
        return $this->_get('aw_reward_points_invoiced');
    }

    /**
     * @param float $awRewardPointsInvoiced
     * @return $this
     */
    public function setAwRewardPointsInvoiced($awRewardPointsInvoiced)
    {
        $this->setData('aw_reward_points_invoiced', $awRewardPointsInvoiced);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwRewardPointsRefunded()
    {
        return $this->_get('base_aw_reward_points_refunded');
    }

    /**
     * @param float $baseAwRewardPointsRefunded
     * @return $this
     */
    public function setBaseAwRewardPointsRefunded($baseAwRewardPointsRefunded)
    {
        $this->setData('base_aw_reward_points_refunded', $baseAwRewardPointsRefunded);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwRewardPointsRefunded()
    {
        return $this->_get('aw_reward_points_refunded');
    }

    /**
     * @param float $awRewardPointsRefunded
     * @return $this
     */
    public function setAwRewardPointsRefunded($awRewardPointsRefunded)
    {
        $this->setData('aw_reward_points_refunded', $awRewardPointsRefunded);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAwRewardPointsBlnceInvoiced()
    {
        return $this->_get('aw_reward_points_blnce_invoiced');
    }

    /**
     * @param int $awRewardPointsBlnceInvoiced
     * @return $this
     */
    public function setAwRewardPointsBlnceInvoiced($awRewardPointsBlnceInvoiced)
    {
        $this->setData('aw_reward_points_blnce_invoiced', $awRewardPointsBlnceInvoiced);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAwRewardPointsBlnceRefunded()
    {
        return $this->_get('aw_reward_points_blnce_refunded');
    }

    /**
     * @param int $awRewardPointsBlnceRefunded
     * @return $this
     */
    public function setAwRewardPointsBlnceRefunded($awRewardPointsBlnceRefunded)
    {
        $this->setData('aw_reward_points_blnce_refunded', $awRewardPointsBlnceRefunded);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwRewardPointsRefund()
    {
        return $this->_get('base_aw_reward_points_refund');
    }

    /**
     * @param float $baseAwRewardPointsRefund
     * @return $this
     */
    public function setBaseAwRewardPointsRefund($baseAwRewardPointsRefund)
    {
        $this->setData('base_aw_reward_points_refund', $baseAwRewardPointsRefund);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwRewardPointsRefund()
    {
        return $this->_get('aw_reward_points_refund');
    }

    /**
     * @param float $awRewardPointsRefund
     * @return $this
     */
    public function setAwRewardPointsRefund($awRewardPointsRefund)
    {
        $this->setData('aw_reward_points_refund', $awRewardPointsRefund);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAwRewardPointsBlnceRefund()
    {
        return $this->_get('aw_reward_points_blnce_refund');
    }

    /**
     * @param int $awRewardPointsBlnceRefund
     * @return $this
     */
    public function setAwRewardPointsBlnceRefund($awRewardPointsBlnceRefund)
    {
        $this->setData('aw_reward_points_blnce_refund', $awRewardPointsBlnceRefund);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwRewardPointsReimbursed()
    {
        return $this->_get('base_aw_reward_points_reimbursed');
    }

    /**
     * @param float $baseAwRewardPointsReimbursed
     * @return $this
     */
    public function setBaseAwRewardPointsReimbursed($baseAwRewardPointsReimbursed)
    {
        $this->setData('base_aw_reward_points_reimbursed', $baseAwRewardPointsReimbursed);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwRewardPointsReimbursed()
    {
        return $this->_get('aw_reward_points_reimbursed');
    }

    /**
     * @param float $awRewardPointsReimbursed
     * @return $this
     */
    public function setAwRewardPointsReimbursed($awRewardPointsReimbursed)
    {
        $this->setData('aw_reward_points_reimbursed', $awRewardPointsReimbursed);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAwRewardPointsBlnceReimbursed()
    {
        return $this->_get('aw_reward_points_blnce_reimbursed');
    }

    /**
     * @param int $awRewardPointsBlnceReimbursed
     * @return $this
     */
    public function setAwRewardPointsBlnceReimbursed($awRewardPointsBlnceReimbursed)
    {
        $this->setData('aw_reward_points_blnce_reimbursed', $awRewardPointsBlnceReimbursed);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwGiftcardAmount()
    {
        return $this->_get('aw_giftcard_amount');
    }

    /**
     * @param float $awGiftcardAmount
     * @return $this
     */
    public function setAwGiftcardAmount($awGiftcardAmount)
    {
        $this->setData('aw_giftcard_amount', $awGiftcardAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwGiftcardAmount()
    {
        return $this->_get('base_aw_giftcard_amount');
    }

    /**
     * @param float $baseAwGiftcardAmount
     * @return $this
     */
    public function setBaseAwGiftcardAmount($baseAwGiftcardAmount)
    {
        $this->setData('base_aw_giftcard_amount', $baseAwGiftcardAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwGiftcardInvoiced()
    {
        return $this->_get('aw_giftcard_invoiced');
    }

    /**
     * @param float $awGiftcardInvoiced
     * @return $this
     */
    public function setAwGiftcardInvoiced($awGiftcardInvoiced)
    {
        $this->setData('aw_giftcard_invoiced', $awGiftcardInvoiced);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwGiftcardInvoiced()
    {
        return $this->_get('base_aw_giftcard_invoiced');
    }

    /**
     * @param float $baseAwGiftcardInvoiced
     * @return $this
     */
    public function setBaseAwGiftcardInvoiced($baseAwGiftcardInvoiced)
    {
        $this->setData('base_aw_giftcard_invoiced', $baseAwGiftcardInvoiced);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAwGiftcardRefunded()
    {
        return $this->_get('aw_giftcard_refunded');
    }

    /**
     * @param float $awGiftcardRefunded
     * @return $this
     */
    public function setAwGiftcardRefunded($awGiftcardRefunded)
    {
        $this->setData('aw_giftcard_refunded', $awGiftcardRefunded);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseAwGiftcardRefunded()
    {
        return $this->_get('base_aw_giftcard_refunded');
    }

    /**
     * @param float $baseAwGiftcardRefunded
     * @return $this
     */
    public function setBaseAwGiftcardRefunded($baseAwGiftcardRefunded)
    {
        $this->setData('base_aw_giftcard_refunded', $baseAwGiftcardRefunded);
        return $this;
    }

    /**
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface[]|null
     */
    public function getAwGiftcardCodes()
    {
        return $this->_get('aw_giftcard_codes');
    }

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface[] $awGiftcardCodes
     * @return $this
     */
    public function setAwGiftcardCodes($awGiftcardCodes)
    {
        $this->setData('aw_giftcard_codes', $awGiftcardCodes);
        return $this;
    }

    /**
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterface[]|null
     */
    public function getAwGiftcardCodesInvoiced()
    {
        return $this->_get('aw_giftcard_codes_invoiced');
    }

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterface[] $awGiftcardCodesInvoiced
     * @return $this
     */
    public function setAwGiftcardCodesInvoiced($awGiftcardCodesInvoiced)
    {
        $this->setData('aw_giftcard_codes_invoiced', $awGiftcardCodesInvoiced);
        return $this;
    }

    /**
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface[]|null
     */
    public function getAwGiftcardCodesRefunded()
    {
        return $this->_get('aw_giftcard_codes_refunded');
    }

    /**
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface[] $awGiftcardCodesRefunded
     * @return $this
     */
    public function setAwGiftcardCodesRefunded($awGiftcardCodesRefunded)
    {
        $this->setData('aw_giftcard_codes_refunded', $awGiftcardCodesRefunded);
        return $this;
    }
}
