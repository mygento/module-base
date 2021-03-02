<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class OrderItemMock
 * @package Mygento\Base\Test
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderItemMock extends DataObject implements OrderItemInterface
{
    public function setParentItem($item)
    {
        return parent::setParentItem($item);
    }

    public function getParentItem()
    {
        return parent::getParentItem();
    }

    public function getOriginalPrice()
    {
        return parent::getOriginalPrice();
    }

    public function getAdditionalData()
    {
        return parent::getAdditionalData();
    }

    public function getAmountRefunded()
    {
        return parent::getAmountRefunded();
    }

    public function getAppliedRuleIds()
    {
        return parent::getAppliedRuleIds();
    }

    public function getBaseAmountRefunded()
    {
        return parent::getBaseAmountRefunded();
    }

    public function getBaseCost()
    {
        return parent::getBaseCost();
    }

    public function getBaseDiscountAmount()
    {
        return parent::getBaseDiscountAmount();
    }

    public function getBaseDiscountInvoiced()
    {
        return parent::getBaseDiscountInvoiced();
    }

    public function getBaseDiscountRefunded()
    {
        return parent::getBaseDiscountRefunded();
    }

    public function getBaseDiscountTaxCompensationAmount()
    {
        return parent::getBaseDiscountTaxCompensationAmount();
    }

    public function getBaseDiscountTaxCompensationInvoiced()
    {
        return parent::getBaseDiscountTaxCompensationInvoiced();
    }

    public function getBaseDiscountTaxCompensationRefunded()
    {
        return parent::getBaseDiscountTaxCompensationRefunded();
    }

    public function getBaseOriginalPrice()
    {
        return parent::getBaseOriginalPrice();
    }

    public function getBasePrice()
    {
        return parent::getBasePrice();
    }

    public function getBasePriceInclTax()
    {
        return parent::getBasePriceInclTax();
    }

    public function getBaseRowInvoiced()
    {
        return parent::getBaseRowInvoiced();
    }

    public function getBaseRowTotal()
    {
        return parent::getBaseRowTotal();
    }

    public function getBaseRowTotalInclTax()
    {
        return parent::getBaseRowTotalInclTax();
    }

    public function getBaseTaxAmount()
    {
        return parent::getBaseTaxAmount();
    }

    public function getBaseTaxBeforeDiscount()
    {
        return parent::getBaseTaxBeforeDiscount();
    }

    public function getBaseTaxInvoiced()
    {
        return parent::getBaseTaxInvoiced();
    }

    public function getBaseTaxRefunded()
    {
        return parent::getBaseTaxRefunded();
    }

    public function getBaseWeeeTaxAppliedAmount()
    {
        return parent::getBaseWeeeTaxAppliedAmount();
    }

    public function getBaseWeeeTaxAppliedRowAmnt()
    {
        return parent::getBaseWeeeTaxAppliedRowAmnt();
    }

    public function getBaseWeeeTaxDisposition()
    {
        return parent::getBaseWeeeTaxDisposition();
    }

    public function getBaseWeeeTaxRowDisposition()
    {
        return parent::getBaseWeeeTaxRowDisposition();
    }

    public function getCreatedAt()
    {
        return parent::getCreatedAt();
    }

    public function setCreatedAt($createdAt)
    {
        return parent::setCreatedAt($createdAt);
    }

    public function getDescription()
    {
        return parent::getDescription();
    }

    public function getDiscountAmount()
    {
        return parent::getDiscountAmount();
    }

    public function getDiscountInvoiced()
    {
        return parent::getDiscountInvoiced();
    }

    public function getDiscountPercent()
    {
        return parent::getDiscountPercent();
    }

    public function getDiscountRefunded()
    {
        return parent::getDiscountRefunded();
    }

    public function getEventId()
    {
        return parent::getEventId();
    }

    public function getExtOrderItemId()
    {
        return parent::getExtOrderItemId();
    }

    public function getFreeShipping()
    {
        return parent::getFreeShipping();
    }

    public function getGwBasePrice()
    {
        return parent::getGwBasePrice();
    }

    public function getGwBasePriceInvoiced()
    {
        return parent::getGwBasePriceInvoiced();
    }

    public function getGwBasePriceRefunded()
    {
        return parent::getGwBasePriceRefunded();
    }

    public function getGwBaseTaxAmount()
    {
        return parent::getGwBaseTaxAmount();
    }

    public function getGwBaseTaxAmountInvoiced()
    {
        return parent::getGwBaseTaxAmountInvoiced();
    }

    public function getGwBaseTaxAmountRefunded()
    {
        return parent::getGwBaseTaxAmountRefunded();
    }

    public function getGwId()
    {
        return parent::getGwId();
    }

    public function getGwPrice()
    {
        return parent::getGwPrice();
    }

    public function getGwPriceInvoiced()
    {
        return parent::getGwPriceInvoiced();
    }

    public function getGwPriceRefunded()
    {
        return parent::getGwPriceRefunded();
    }

    public function getGwTaxAmount()
    {
        return parent::getGwTaxAmount();
    }

    public function getGwTaxAmountInvoiced()
    {
        return parent::getGwTaxAmountInvoiced();
    }

    public function getGwTaxAmountRefunded()
    {
        return parent::getGwTaxAmountRefunded();
    }

    public function getDiscountTaxCompensationAmount()
    {
        return parent::getDiscountTaxCompensationAmount();
    }

    public function getDiscountTaxCompensationCanceled()
    {
        return parent::getDiscountTaxCompensationCanceled();
    }

    public function getDiscountTaxCompensationInvoiced()
    {
        return parent::getDiscountTaxCompensationInvoiced();
    }

    public function getDiscountTaxCompensationRefunded()
    {
        return parent::getDiscountTaxCompensationRefunded();
    }

    public function getIsQtyDecimal()
    {
        return parent::getIsQtyDecimal();
    }

    public function getIsVirtual()
    {
        return parent::getIsVirtual();
    }

    public function getItemId()
    {
        return parent::getItemId();
    }

    public function getLockedDoInvoice()
    {
        return parent::getLockedDoInvoice();
    }

    public function getLockedDoShip()
    {
        return parent::getLockedDoShip();
    }

    public function getName()
    {
        return parent::getName();
    }

    public function getNoDiscount()
    {
        return parent::getNoDiscount();
    }

    public function getOrderId()
    {
        return parent::getOrderId();
    }

    public function getParentItemId()
    {
        return parent::getParentItemId();
    }

    public function getPrice()
    {
        return parent::getPrice();
    }

    public function getPriceInclTax()
    {
        return parent::getPriceInclTax();
    }

    public function getProductId()
    {
        return parent::getProductId();
    }

    public function getProductType()
    {
        return parent::getProductType();
    }

    public function getQtyBackordered()
    {
        return parent::getQtyBackordered();
    }

    public function getQtyCanceled()
    {
        return parent::getQtyCanceled();
    }

    public function getQtyInvoiced()
    {
        return parent::getQtyInvoiced();
    }

    public function getQtyOrdered()
    {
        return parent::getQtyOrdered();
    }

    public function getQtyRefunded()
    {
        return parent::getQtyRefunded();
    }

    public function getQtyReturned()
    {
        return parent::getQtyReturned();
    }

    public function getQtyShipped()
    {
        return parent::getQtyShipped();
    }

    public function getQuoteItemId()
    {
        return parent::getQuoteItemId();
    }

    public function getRowInvoiced()
    {
        return parent::getRowInvoiced();
    }

    public function getRowTotal()
    {
        return parent::getRowTotal();
    }

    public function getRowTotalInclTax()
    {
        return parent::getRowTotalInclTax();
    }

    public function getRowWeight()
    {
        return parent::getRowWeight();
    }

    public function getSku()
    {
        return parent::getSku();
    }

    public function getStoreId()
    {
        return parent::getStoreId();
    }

    public function getTaxAmount()
    {
        return parent::getTaxAmount();
    }

    public function getTaxBeforeDiscount()
    {
        return parent::getTaxBeforeDiscount();
    }

    public function getTaxCanceled()
    {
        return parent::getTaxCanceled();
    }

    public function getTaxInvoiced()
    {
        return parent::getTaxInvoiced();
    }

    public function getTaxPercent()
    {
        return parent::getTaxPercent();
    }

    public function getTaxRefunded()
    {
        return parent::getTaxRefunded();
    }

    public function getUpdatedAt()
    {
        return parent::getUpdatedAt();
    }

    public function getWeeeTaxApplied()
    {
        return parent::getWeeeTaxApplied();
    }

    public function getWeeeTaxAppliedAmount()
    {
        return parent::getWeeeTaxAppliedAmount();
    }

    public function getWeeeTaxAppliedRowAmount()
    {
        return parent::getWeeeTaxAppliedRowAmount();
    }

    public function getWeeeTaxDisposition()
    {
        return parent::getWeeeTaxDisposition();
    }

    public function getWeeeTaxRowDisposition()
    {
        return parent::getWeeeTaxRowDisposition();
    }

    public function getWeight()
    {
        return parent::getWeight();
    }

    public function setUpdatedAt($timestamp)
    {
        return parent::setUpdatedAt($timestamp);
    }

    public function setItemId($id)
    {
        return parent::setItemId($id);
    }

    public function setOrderId($id)
    {
        return parent::setOrderId($id);
    }

    public function setParentItemId($id)
    {
        return parent::setParentItemId($id);
    }

    public function setQuoteItemId($id)
    {
        return parent::setQuoteItemId($id);
    }

    public function setStoreId($id)
    {
        return parent::setStoreId($id);
    }

    public function setProductId($id)
    {
        return parent::setProductId($id);
    }

    public function setProductType($productType)
    {
        return parent::setProductType($productType);
    }

    public function setWeight($weight)
    {
        return parent::setWeight($weight);
    }

    public function setIsVirtual($isVirtual)
    {
        return parent::setIsVirtual($isVirtual);
    }

    public function setSku($sku)
    {
        return parent::setSku($sku);
    }

    public function setName($name)
    {
        return parent::setName($name);
    }

    public function setDescription($description)
    {
        return parent::setDescription($description);
    }

    public function setAppliedRuleIds($appliedRuleIds)
    {
        return parent::setAppliedRuleIds($appliedRuleIds);
    }

    public function setAdditionalData($additionalData)
    {
        return parent::setAdditionalData($additionalData);
    }

    public function setIsQtyDecimal($isQtyDecimal)
    {
        return parent::setIsQtyDecimal($isQtyDecimal);
    }

    public function setNoDiscount($noDiscount)
    {
        return parent::setNoDiscount($noDiscount);
    }

    public function setQtyBackordered($qtyBackordered)
    {
        return parent::setQtyBackordered($qtyBackordered);
    }

    public function setQtyCanceled($qtyCanceled)
    {
        return parent::setQtyCanceled($qtyCanceled);
    }

    public function setQtyInvoiced($qtyInvoiced)
    {
        return parent::setQtyInvoiced($qtyInvoiced);
    }

    public function setQtyOrdered($qtyOrdered)
    {
        return parent::setQtyOrdered($qtyOrdered);
    }

    public function setQtyRefunded($qtyRefunded)
    {
        return parent::setQtyRefunded($qtyRefunded);
    }

    public function setQtyShipped($qtyShipped)
    {
        return parent::setQtyShipped($qtyShipped);
    }

    public function setBaseCost($baseCost)
    {
        return parent::setBaseCost($baseCost);
    }

    public function setPrice($price)
    {
        return parent::setPrice($price);
    }

    public function setBasePrice($price)
    {
        return parent::setBasePrice($price);
    }

    public function setOriginalPrice($price)
    {
        return parent::setOriginalPrice($price);
    }

    public function setBaseOriginalPrice($price)
    {
        return parent::setBaseOriginalPrice($price);
    }

    public function setTaxPercent($taxPercent)
    {
        return parent::setTaxPercent($taxPercent);
    }

    public function setTaxAmount($amount)
    {
        return parent::setTaxAmount($amount);
    }

    public function setBaseTaxAmount($amount)
    {
        return parent::setBaseTaxAmount($amount);
    }

    public function setTaxInvoiced($taxInvoiced)
    {
        return parent::setTaxInvoiced($taxInvoiced);
    }

    public function setBaseTaxInvoiced($baseTaxInvoiced)
    {
        return parent::setBaseTaxInvoiced($baseTaxInvoiced);
    }

    public function setDiscountPercent($discountPercent)
    {
        return parent::setDiscountPercent($discountPercent);
    }

    public function setDiscountAmount($amount)
    {
        return parent::setDiscountAmount($amount);
    }

    public function setBaseDiscountAmount($amount)
    {
        return parent::setBaseDiscountAmount($amount);
    }

    public function setDiscountInvoiced($discountInvoiced)
    {
        return parent::setDiscountInvoiced($discountInvoiced);
    }

    public function setBaseDiscountInvoiced($baseDiscountInvoiced)
    {
        return parent::setBaseDiscountInvoiced($baseDiscountInvoiced);
    }

    public function setAmountRefunded($amountRefunded)
    {
        return parent::setAmountRefunded($amountRefunded);
    }

    public function setBaseAmountRefunded($baseAmountRefunded)
    {
        return parent::setBaseAmountRefunded($baseAmountRefunded);
    }

    public function setRowTotal($amount)
    {
        return parent::setRowTotal($amount);
    }

    public function setBaseRowTotal($amount)
    {
        return parent::setBaseRowTotal($amount);
    }

    public function setRowInvoiced($rowInvoiced)
    {
        return parent::setRowInvoiced($rowInvoiced);
    }

    public function setBaseRowInvoiced($baseRowInvoiced)
    {
        return parent::setBaseRowInvoiced($baseRowInvoiced);
    }

    public function setRowWeight($rowWeight)
    {
        return parent::setRowWeight($rowWeight);
    }

    public function setBaseTaxBeforeDiscount($baseTaxBeforeDiscount)
    {
        return parent::setBaseTaxBeforeDiscount($baseTaxBeforeDiscount);
    }

    public function setTaxBeforeDiscount($taxBeforeDiscount)
    {
        return parent::setTaxBeforeDiscount($taxBeforeDiscount);
    }

    public function setExtOrderItemId($id)
    {
        return parent::setExtOrderItemId($id);
    }

    public function setLockedDoInvoice($flag)
    {
        return parent::setLockedDoInvoice($flag);
    }

    public function setLockedDoShip($flag)
    {
        return parent::setLockedDoShip($flag);
    }

    public function setPriceInclTax($amount)
    {
        return parent::setPriceInclTax($amount);
    }

    public function setBasePriceInclTax($amount)
    {
        return parent::setBasePriceInclTax($amount);
    }

    public function setRowTotalInclTax($amount)
    {
        return parent::setRowTotalInclTax($amount);
    }

    public function setBaseRowTotalInclTax($amount)
    {
        return parent::setBaseRowTotalInclTax($amount);
    }

    public function setDiscountTaxCompensationAmount($amount)
    {
        return parent::setDiscountTaxCompensationAmount($amount);
    }

    public function setBaseDiscountTaxCompensationAmount($amount)
    {
        return parent::setBaseDiscountTaxCompensationAmount($amount);
    }

    public function setDiscountTaxCompensationInvoiced($discountTaxCompensationInvoiced)
    {
        return parent::setDiscountTaxCompensationInvoiced(
            $discountTaxCompensationInvoiced
        );
    }

    public function setBaseDiscountTaxCompensationInvoiced($baseDiscountTaxCompensationInvoiced)
    {
        return parent::setBaseDiscountTaxCompensationInvoiced(
            $baseDiscountTaxCompensationInvoiced
        );
    }

    public function setDiscountTaxCompensationRefunded($discountTaxCompensationRefunded)
    {
        return parent::setDiscountTaxCompensationRefunded(
            $discountTaxCompensationRefunded
        );
    }

    public function setBaseDiscountTaxCompensationRefunded($baseDiscountTaxCompensationRefunded)
    {
        return parent::setBaseDiscountTaxCompensationRefunded(
            $baseDiscountTaxCompensationRefunded
        );
    }

    public function setTaxCanceled($taxCanceled)
    {
        return parent::setTaxCanceled($taxCanceled);
    }

    public function setDiscountTaxCompensationCanceled($discountTaxCompensationCanceled)
    {
        return parent::setDiscountTaxCompensationCanceled(
            $discountTaxCompensationCanceled
        );
    }

    public function setTaxRefunded($taxRefunded)
    {
        return parent::setTaxRefunded($taxRefunded);
    }

    public function setBaseTaxRefunded($baseTaxRefunded)
    {
        return parent::setBaseTaxRefunded($baseTaxRefunded);
    }

    public function setDiscountRefunded($discountRefunded)
    {
        return parent::setDiscountRefunded($discountRefunded);
    }

    public function setBaseDiscountRefunded($baseDiscountRefunded)
    {
        return parent::setBaseDiscountRefunded($baseDiscountRefunded);
    }

    public function setGwId($id)
    {
        return parent::setGwId($id);
    }

    public function setGwBasePrice($price)
    {
        return parent::setGwBasePrice($price);
    }

    public function setGwPrice($price)
    {
        return parent::setGwPrice($price);
    }

    public function setGwBaseTaxAmount($amount)
    {
        return parent::setGwBaseTaxAmount($amount);
    }

    public function setGwTaxAmount($amount)
    {
        return parent::setGwTaxAmount($amount);
    }

    public function setGwBasePriceInvoiced($gwBasePriceInvoiced)
    {
        return parent::setGwBasePriceInvoiced($gwBasePriceInvoiced);
    }

    public function setGwPriceInvoiced($gwPriceInvoiced)
    {
        return parent::setGwPriceInvoiced($gwPriceInvoiced);
    }

    public function setGwBaseTaxAmountInvoiced($gwBaseTaxAmountInvoiced)
    {
        return parent::setGwBaseTaxAmountInvoiced($gwBaseTaxAmountInvoiced);
    }

    public function setGwTaxAmountInvoiced($gwTaxAmountInvoiced)
    {
        return parent::setGwTaxAmountInvoiced($gwTaxAmountInvoiced);
    }

    public function setGwBasePriceRefunded($gwBasePriceRefunded)
    {
        return parent::setGwBasePriceRefunded($gwBasePriceRefunded);
    }

    public function setGwPriceRefunded($gwPriceRefunded)
    {
        return parent::setGwPriceRefunded($gwPriceRefunded);
    }

    public function setGwBaseTaxAmountRefunded($gwBaseTaxAmountRefunded)
    {
        return parent::setGwBaseTaxAmountRefunded($gwBaseTaxAmountRefunded);
    }

    public function setGwTaxAmountRefunded($gwTaxAmountRefunded)
    {
        return parent::setGwTaxAmountRefunded($gwTaxAmountRefunded);
    }

    public function setFreeShipping($freeShipping)
    {
        return parent::setFreeShipping($freeShipping);
    }

    public function setQtyReturned($qtyReturned)
    {
        return parent::setQtyReturned($qtyReturned);
    }

    public function setEventId($id)
    {
        return parent::setEventId($id);
    }

    public function setBaseWeeeTaxAppliedAmount($amount)
    {
        return parent::setBaseWeeeTaxAppliedAmount($amount);
    }

    public function setBaseWeeeTaxAppliedRowAmnt($amnt)
    {
        return parent::setBaseWeeeTaxAppliedRowAmnt($amnt);
    }

    public function setWeeeTaxAppliedAmount($amount)
    {
        return parent::setWeeeTaxAppliedAmount($amount);
    }

    public function setWeeeTaxAppliedRowAmount($amount)
    {
        return parent::setWeeeTaxAppliedRowAmount($amount);
    }

    public function setWeeeTaxApplied($weeeTaxApplied)
    {
        return parent::setWeeeTaxApplied($weeeTaxApplied);
    }

    public function setWeeeTaxDisposition($weeeTaxDisposition)
    {
        return parent::setWeeeTaxDisposition($weeeTaxDisposition);
    }

    public function setWeeeTaxRowDisposition($weeeTaxRowDisposition)
    {
        return parent::setWeeeTaxRowDisposition($weeeTaxRowDisposition);
    }

    public function setBaseWeeeTaxDisposition($baseWeeeTaxDisposition)
    {
        return parent::setBaseWeeeTaxDisposition($baseWeeeTaxDisposition);
    }

    public function setBaseWeeeTaxRowDisposition($baseWeeeTaxRowDisposition)
    {
        return parent::setBaseWeeeTaxRowDisposition($baseWeeeTaxRowDisposition);
    }

    public function getProductOption()
    {
        return parent::getProductOption();
    }

    public function setProductOption(\Magento\Catalog\Api\Data\ProductOptionInterface $productOption)
    {
        return parent::setProductOption($productOption);
    }

    public function getExtensionAttributes()
    {
        return parent::getExtensionAttributes();
    }

    public function setExtensionAttributes(\Magento\Sales\Api\Data\OrderItemExtensionInterface $extensionAttributes)
    {
        return parent::setExtensionAttributes($extensionAttributes);
    }
}
