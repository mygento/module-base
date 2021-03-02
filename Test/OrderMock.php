<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class OrderMock
 * @package Mygento\Base\Test
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderMock extends DataObject implements OrderInterface
{
    public function getPayment()
    {
        return parent::getPayment();
    }

    public function setBillingAddress(\Magento\Sales\Api\Data\OrderAddressInterface $address = null)
    {
        return parent::setBillingAddress($address);
    }

    public function getBillingAddress()
    {
        return parent::getBillingAddress();
    }

    public function setState($state)
    {
        return parent::setState($state);
    }

    public function setPayment(\Magento\Sales\Api\Data\OrderPaymentInterface $payment = null)
    {
        return parent::setPayment($payment);
    }

    public function getTotalDue()
    {
        return parent::getTotalDue();
    }

    public function getBaseTotalDue()
    {
        return parent::getBaseTotalDue();
    }

    public function getIncrementId()
    {
        return parent::getIncrementId();
    }

    public function getItems()
    {
        return parent::getItems();
    }

    public function setItems($items)
    {
        return parent::setItems($items);
    }

    public function getStatusHistories()
    {
        return parent::getStatusHistories();
    }

    public function getExtensionAttributes()
    {
        return parent::getExtensionAttributes();
    }

    public function setExtensionAttributes(\Magento\Sales\Api\Data\OrderExtensionInterface $extensionAttributes)
    {
        return parent::setExtensionAttributes($extensionAttributes);
    }

    public function getAdjustmentNegative()
    {
        return parent::getAdjustmentNegative();
    }

    public function getAdjustmentPositive()
    {
        return parent::getAdjustmentPositive();
    }

    public function getAppliedRuleIds()
    {
        return parent::getAppliedRuleIds();
    }

    public function getBaseAdjustmentNegative()
    {
        return parent::getBaseAdjustmentNegative();
    }

    public function getBaseAdjustmentPositive()
    {
        return parent::getBaseAdjustmentPositive();
    }

    public function getBaseCurrencyCode()
    {
        return parent::getBaseCurrencyCode();
    }

    public function getBaseDiscountAmount()
    {
        return parent::getBaseDiscountAmount();
    }

    public function getBaseDiscountCanceled()
    {
        return parent::getBaseDiscountCanceled();
    }

    public function getBaseDiscountInvoiced()
    {
        return parent::getBaseDiscountInvoiced();
    }

    public function getBaseDiscountRefunded()
    {
        return parent::getBaseDiscountRefunded();
    }

    public function getBaseGrandTotal()
    {
        return parent::getBaseGrandTotal();
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

    public function getBaseShippingAmount()
    {
        return parent::getBaseShippingAmount();
    }

    public function getBaseShippingCanceled()
    {
        return parent::getBaseShippingCanceled();
    }

    public function getBaseShippingDiscountAmount()
    {
        return parent::getBaseShippingDiscountAmount();
    }

    public function getBaseShippingDiscountTaxCompensationAmnt()
    {
        return parent::getBaseShippingDiscountTaxCompensationAmnt();
    }

    public function getBaseShippingInclTax()
    {
        return parent::getBaseShippingInclTax();
    }

    public function getBaseShippingInvoiced()
    {
        return parent::getBaseShippingInvoiced();
    }

    public function getBaseShippingRefunded()
    {
        return parent::getBaseShippingRefunded();
    }

    public function getBaseShippingTaxAmount()
    {
        return parent::getBaseShippingTaxAmount();
    }

    public function getBaseShippingTaxRefunded()
    {
        return parent::getBaseShippingTaxRefunded();
    }

    public function getBaseSubtotal()
    {
        return parent::getBaseSubtotal();
    }

    public function getBaseSubtotalCanceled()
    {
        return parent::getBaseSubtotalCanceled();
    }

    public function getBaseSubtotalInclTax()
    {
        return parent::getBaseSubtotalInclTax();
    }

    public function getBaseSubtotalInvoiced()
    {
        return parent::getBaseSubtotalInvoiced();
    }

    public function getBaseSubtotalRefunded()
    {
        return parent::getBaseSubtotalRefunded();
    }

    public function getBaseTaxAmount()
    {
        return parent::getBaseTaxAmount();
    }

    public function getBaseTaxCanceled()
    {
        return parent::getBaseTaxCanceled();
    }

    public function getBaseTaxInvoiced()
    {
        return parent::getBaseTaxInvoiced();
    }

    public function getBaseTaxRefunded()
    {
        return parent::getBaseTaxRefunded();
    }

    public function getBaseTotalCanceled()
    {
        return parent::getBaseTotalCanceled();
    }

    public function getBaseTotalInvoiced()
    {
        return parent::getBaseTotalInvoiced();
    }

    public function getBaseTotalInvoicedCost()
    {
        return parent::getBaseTotalInvoicedCost();
    }

    public function getBaseTotalOfflineRefunded()
    {
        return parent::getBaseTotalOfflineRefunded();
    }

    public function getBaseTotalOnlineRefunded()
    {
        return parent::getBaseTotalOnlineRefunded();
    }

    public function getBaseTotalPaid()
    {
        return parent::getBaseTotalPaid();
    }

    public function getBaseTotalQtyOrdered()
    {
        return parent::getBaseTotalQtyOrdered();
    }

    public function getBaseTotalRefunded()
    {
        return parent::getBaseTotalRefunded();
    }

    public function getBaseToGlobalRate()
    {
        return parent::getBaseToGlobalRate();
    }

    public function getBaseToOrderRate()
    {
        return parent::getBaseToOrderRate();
    }

    public function getBillingAddressId()
    {
        return parent::getBillingAddressId();
    }

    public function getCanShipPartially()
    {
        return parent::getCanShipPartially();
    }

    public function getCanShipPartiallyItem()
    {
        return parent::getCanShipPartiallyItem();
    }

    public function getCouponCode()
    {
        return parent::getCouponCode();
    }

    public function getCreatedAt()
    {
        return parent::getCreatedAt();
    }

    public function setCreatedAt($createdAt)
    {
        return parent::setCreatedAt($createdAt);
    }

    public function getCustomerDob()
    {
        return parent::getCustomerDob();
    }

    public function getCustomerEmail()
    {
        return parent::getCustomerEmail();
    }

    public function getCustomerFirstname()
    {
        return parent::getCustomerFirstname();
    }

    public function getCustomerGender()
    {
        return parent::getCustomerGender();
    }

    public function getCustomerGroupId()
    {
        return parent::getCustomerGroupId();
    }

    public function getCustomerId()
    {
        return parent::getCustomerId();
    }

    public function getCustomerIsGuest()
    {
        return parent::getCustomerIsGuest();
    }

    public function getCustomerLastname()
    {
        return parent::getCustomerLastname();
    }

    public function getCustomerMiddlename()
    {
        return parent::getCustomerMiddlename();
    }

    public function getCustomerNote()
    {
        return parent::getCustomerNote();
    }

    public function getCustomerNoteNotify()
    {
        return parent::getCustomerNoteNotify();
    }

    public function getCustomerPrefix()
    {
        return parent::getCustomerPrefix();
    }

    public function getCustomerSuffix()
    {
        return parent::getCustomerSuffix();
    }

    public function getCustomerTaxvat()
    {
        return parent::getCustomerTaxvat();
    }

    public function getDiscountAmount()
    {
        return parent::getDiscountAmount();
    }

    public function getDiscountCanceled()
    {
        return parent::getDiscountCanceled();
    }

    public function getDiscountDescription()
    {
        return parent::getDiscountDescription();
    }

    public function getDiscountInvoiced()
    {
        return parent::getDiscountInvoiced();
    }

    public function getDiscountRefunded()
    {
        return parent::getDiscountRefunded();
    }

    public function getEditIncrement()
    {
        return parent::getEditIncrement();
    }

    public function getEmailSent()
    {
        return parent::getEmailSent();
    }

    public function getExtCustomerId()
    {
        return parent::getExtCustomerId();
    }

    public function getExtOrderId()
    {
        return parent::getExtOrderId();
    }

    public function getForcedShipmentWithInvoice()
    {
        return parent::getForcedShipmentWithInvoice();
    }

    public function getGlobalCurrencyCode()
    {
        return parent::getGlobalCurrencyCode();
    }

    public function getGrandTotal()
    {
        return parent::getGrandTotal();
    }

    public function getDiscountTaxCompensationAmount()
    {
        return parent::getDiscountTaxCompensationAmount();
    }

    public function getDiscountTaxCompensationInvoiced()
    {
        return parent::getDiscountTaxCompensationInvoiced();
    }

    public function getDiscountTaxCompensationRefunded()
    {
        return parent::getDiscountTaxCompensationRefunded();
    }

    public function getHoldBeforeState()
    {
        return parent::getHoldBeforeState();
    }

    public function getHoldBeforeStatus()
    {
        return parent::getHoldBeforeStatus();
    }

    public function getIsVirtual()
    {
        return parent::getIsVirtual();
    }

    public function getOrderCurrencyCode()
    {
        return parent::getOrderCurrencyCode();
    }

    public function getOriginalIncrementId()
    {
        return parent::getOriginalIncrementId();
    }

    public function getPaymentAuthorizationAmount()
    {
        return parent::getPaymentAuthorizationAmount();
    }

    public function getPaymentAuthExpiration()
    {
        return parent::getPaymentAuthExpiration();
    }

    public function getProtectCode()
    {
        return parent::getProtectCode();
    }

    public function getQuoteAddressId()
    {
        return parent::getQuoteAddressId();
    }

    public function getQuoteId()
    {
        return parent::getQuoteId();
    }

    public function getRelationChildId()
    {
        return parent::getRelationChildId();
    }

    public function getRelationChildRealId()
    {
        return parent::getRelationChildRealId();
    }

    public function getRelationParentId()
    {
        return parent::getRelationParentId();
    }

    public function getRelationParentRealId()
    {
        return parent::getRelationParentRealId();
    }

    public function getRemoteIp()
    {
        return parent::getRemoteIp();
    }

    public function getShippingAmount()
    {
        return parent::getShippingAmount();
    }

    public function getShippingCanceled()
    {
        return parent::getShippingCanceled();
    }

    public function getShippingDescription()
    {
        return parent::getShippingDescription();
    }

    public function getShippingDiscountAmount()
    {
        return parent::getShippingDiscountAmount();
    }

    public function getShippingDiscountTaxCompensationAmount()
    {
        return parent::getShippingDiscountTaxCompensationAmount();
    }

    public function getShippingInclTax()
    {
        return parent::getShippingInclTax();
    }

    public function getShippingInvoiced()
    {
        return parent::getShippingInvoiced();
    }

    public function getShippingRefunded()
    {
        return parent::getShippingRefunded();
    }

    public function getShippingTaxAmount()
    {
        return parent::getShippingTaxAmount();
    }

    public function getShippingTaxRefunded()
    {
        return parent::getShippingTaxRefunded();
    }

    public function getState()
    {
        return parent::getState();
    }

    public function getStatus()
    {
        return parent::getStatus();
    }

    public function getStoreCurrencyCode()
    {
        return parent::getStoreCurrencyCode();
    }

    public function getStoreId()
    {
        return parent::getStoreId();
    }

    public function getStoreName()
    {
        return parent::getStoreName();
    }

    public function getStoreToBaseRate()
    {
        return parent::getStoreToBaseRate();
    }

    public function getStoreToOrderRate()
    {
        return parent::getStoreToOrderRate();
    }

    public function getSubtotal()
    {
        return parent::getSubtotal();
    }

    public function getSubtotalCanceled()
    {
        return parent::getSubtotalCanceled();
    }

    public function getSubtotalInclTax()
    {
        return parent::getSubtotalInclTax();
    }

    public function getSubtotalInvoiced()
    {
        return parent::getSubtotalInvoiced();
    }

    public function getSubtotalRefunded()
    {
        return parent::getSubtotalRefunded();
    }

    public function getTaxAmount()
    {
        return parent::getTaxAmount();
    }

    public function getTaxCanceled()
    {
        return parent::getTaxCanceled();
    }

    public function getTaxInvoiced()
    {
        return parent::getTaxInvoiced();
    }

    public function getTaxRefunded()
    {
        return parent::getTaxRefunded();
    }

    public function getTotalCanceled()
    {
        return parent::getTotalCanceled();
    }

    public function getTotalInvoiced()
    {
        return parent::getTotalInvoiced();
    }

    public function getTotalItemCount()
    {
        return parent::getTotalItemCount();
    }

    public function getTotalOfflineRefunded()
    {
        return parent::getTotalOfflineRefunded();
    }

    public function getTotalOnlineRefunded()
    {
        return parent::getTotalOnlineRefunded();
    }

    public function getTotalPaid()
    {
        return parent::getTotalPaid();
    }

    public function getTotalQtyOrdered()
    {
        return parent::getTotalQtyOrdered();
    }

    public function getTotalRefunded()
    {
        return parent::getTotalRefunded();
    }

    public function getUpdatedAt()
    {
        return parent::getUpdatedAt();
    }

    public function getWeight()
    {
        return parent::getWeight();
    }

    public function getXForwardedFor()
    {
        return parent::getXForwardedFor();
    }

    public function setStatusHistories(array $statusHistories = null)
    {
        return parent::setStatusHistories($statusHistories);
    }

    public function setStatus($status)
    {
        return parent::setStatus($status);
    }

    public function setCouponCode($code)
    {
        return parent::setCouponCode($code);
    }

    public function setProtectCode($code)
    {
        return parent::setProtectCode($code);
    }

    public function setShippingDescription($description)
    {
        return parent::setShippingDescription($description);
    }

    public function setIsVirtual($isVirtual)
    {
        return parent::setIsVirtual($isVirtual);
    }

    public function setStoreId($id)
    {
        return parent::setStoreId($id);
    }

    public function setCustomerId($id)
    {
        return parent::setCustomerId($id);
    }

    public function setBaseDiscountAmount($amount)
    {
        return parent::setBaseDiscountAmount($amount);
    }

    public function setBaseDiscountCanceled($baseDiscountCanceled)
    {
        return parent::setBaseDiscountCanceled($baseDiscountCanceled);
    }

    public function setBaseDiscountInvoiced($baseDiscountInvoiced)
    {
        return parent::setBaseDiscountInvoiced($baseDiscountInvoiced);
    }

    public function setBaseDiscountRefunded($baseDiscountRefunded)
    {
        return parent::setBaseDiscountRefunded($baseDiscountRefunded);
    }

    public function setBaseGrandTotal($amount)
    {
        return parent::setBaseGrandTotal($amount);
    }

    public function setBaseShippingAmount($amount)
    {
        return parent::setBaseShippingAmount($amount);
    }

    public function setBaseShippingCanceled($baseShippingCanceled)
    {
        return parent::setBaseShippingCanceled($baseShippingCanceled);
    }

    public function setBaseShippingInvoiced($baseShippingInvoiced)
    {
        return parent::setBaseShippingInvoiced($baseShippingInvoiced);
    }

    public function setBaseShippingRefunded($baseShippingRefunded)
    {
        return parent::setBaseShippingRefunded($baseShippingRefunded);
    }

    public function setBaseShippingTaxAmount($amount)
    {
        return parent::setBaseShippingTaxAmount($amount);
    }

    public function setBaseShippingTaxRefunded($baseShippingTaxRefunded)
    {
        return parent::setBaseShippingTaxRefunded($baseShippingTaxRefunded);
    }

    public function setBaseSubtotal($amount)
    {
        return parent::setBaseSubtotal($amount);
    }

    public function setBaseSubtotalCanceled($baseSubtotalCanceled)
    {
        return parent::setBaseSubtotalCanceled($baseSubtotalCanceled);
    }

    public function setBaseSubtotalInvoiced($baseSubtotalInvoiced)
    {
        return parent::setBaseSubtotalInvoiced($baseSubtotalInvoiced);
    }

    public function setBaseSubtotalRefunded($baseSubtotalRefunded)
    {
        return parent::setBaseSubtotalRefunded($baseSubtotalRefunded);
    }

    public function setBaseTaxAmount($amount)
    {
        return parent::setBaseTaxAmount($amount);
    }

    public function setBaseTaxCanceled($baseTaxCanceled)
    {
        return parent::setBaseTaxCanceled($baseTaxCanceled);
    }

    public function setBaseTaxInvoiced($baseTaxInvoiced)
    {
        return parent::setBaseTaxInvoiced($baseTaxInvoiced);
    }

    public function setBaseTaxRefunded($baseTaxRefunded)
    {
        return parent::setBaseTaxRefunded($baseTaxRefunded);
    }

    public function setBaseToGlobalRate($rate)
    {
        return parent::setBaseToGlobalRate($rate);
    }

    public function setBaseToOrderRate($rate)
    {
        return parent::setBaseToOrderRate($rate);
    }

    public function setBaseTotalCanceled($baseTotalCanceled)
    {
        return parent::setBaseTotalCanceled($baseTotalCanceled);
    }

    public function setBaseTotalInvoiced($baseTotalInvoiced)
    {
        return parent::setBaseTotalInvoiced($baseTotalInvoiced);
    }

    public function setBaseTotalInvoicedCost($baseTotalInvoicedCost)
    {
        return parent::setBaseTotalInvoicedCost($baseTotalInvoicedCost);
    }

    public function setBaseTotalOfflineRefunded($baseTotalOfflineRefunded)
    {
        return parent::setBaseTotalOfflineRefunded($baseTotalOfflineRefunded);
    }

    public function setBaseTotalOnlineRefunded($baseTotalOnlineRefunded)
    {
        return parent::setBaseTotalOnlineRefunded($baseTotalOnlineRefunded);
    }

    public function setBaseTotalPaid($baseTotalPaid)
    {
        return parent::setBaseTotalPaid($baseTotalPaid);
    }

    public function setBaseTotalQtyOrdered($baseTotalQtyOrdered)
    {
        return parent::setBaseTotalQtyOrdered($baseTotalQtyOrdered);
    }

    public function setBaseTotalRefunded($baseTotalRefunded)
    {
        return parent::setBaseTotalRefunded($baseTotalRefunded);
    }

    public function setDiscountAmount($amount)
    {
        return parent::setDiscountAmount($amount);
    }

    public function setDiscountCanceled($discountCanceled)
    {
        return parent::setDiscountCanceled($discountCanceled);
    }

    public function setDiscountInvoiced($discountInvoiced)
    {
        return parent::setDiscountInvoiced($discountInvoiced);
    }

    public function setDiscountRefunded($discountRefunded)
    {
        return parent::setDiscountRefunded($discountRefunded);
    }

    public function setGrandTotal($amount)
    {
        return parent::setGrandTotal($amount);
    }

    public function setShippingAmount($amount)
    {
        return parent::setShippingAmount($amount);
    }

    public function setShippingCanceled($shippingCanceled)
    {
        return parent::setShippingCanceled($shippingCanceled);
    }

    public function setShippingInvoiced($shippingInvoiced)
    {
        return parent::setShippingInvoiced($shippingInvoiced);
    }

    public function setShippingRefunded($shippingRefunded)
    {
        return parent::setShippingRefunded($shippingRefunded);
    }

    public function setShippingTaxAmount($amount)
    {
        return parent::setShippingTaxAmount($amount);
    }

    public function setShippingTaxRefunded($shippingTaxRefunded)
    {
        return parent::setShippingTaxRefunded($shippingTaxRefunded);
    }

    public function setStoreToBaseRate($rate)
    {
        return parent::setStoreToBaseRate($rate);
    }

    public function setStoreToOrderRate($rate)
    {
        return parent::setStoreToOrderRate($rate);
    }

    public function setSubtotal($amount)
    {
        return parent::setSubtotal($amount);
    }

    public function setSubtotalCanceled($subtotalCanceled)
    {
        return parent::setSubtotalCanceled($subtotalCanceled);
    }

    public function setSubtotalInvoiced($subtotalInvoiced)
    {
        return parent::setSubtotalInvoiced($subtotalInvoiced);
    }

    public function setSubtotalRefunded($subtotalRefunded)
    {
        return parent::setSubtotalRefunded($subtotalRefunded);
    }

    public function setTaxAmount($amount)
    {
        return parent::setTaxAmount($amount);
    }

    public function setTaxCanceled($taxCanceled)
    {
        return parent::setTaxCanceled($taxCanceled);
    }

    public function setTaxInvoiced($taxInvoiced)
    {
        return parent::setTaxInvoiced($taxInvoiced);
    }

    public function setTaxRefunded($taxRefunded)
    {
        return parent::setTaxRefunded($taxRefunded);
    }

    public function setTotalCanceled($totalCanceled)
    {
        return parent::setTotalCanceled($totalCanceled);
    }

    public function setTotalInvoiced($totalInvoiced)
    {
        return parent::setTotalInvoiced($totalInvoiced);
    }

    public function setTotalOfflineRefunded($totalOfflineRefunded)
    {
        return parent::setTotalOfflineRefunded($totalOfflineRefunded);
    }

    public function setTotalOnlineRefunded($totalOnlineRefunded)
    {
        return parent::setTotalOnlineRefunded($totalOnlineRefunded);
    }

    public function setTotalPaid($totalPaid)
    {
        return parent::setTotalPaid($totalPaid);
    }

    public function setTotalQtyOrdered($totalQtyOrdered)
    {
        return parent::setTotalQtyOrdered($totalQtyOrdered);
    }

    public function setTotalRefunded($totalRefunded)
    {
        return parent::setTotalRefunded($totalRefunded);
    }

    public function setCanShipPartially($flag)
    {
        return parent::setCanShipPartially($flag);
    }

    public function setCanShipPartiallyItem($flag)
    {
        return parent::setCanShipPartiallyItem($flag);
    }

    public function setCustomerIsGuest($customerIsGuest)
    {
        return parent::setCustomerIsGuest($customerIsGuest);
    }

    public function setCustomerNoteNotify($customerNoteNotify)
    {
        return parent::setCustomerNoteNotify($customerNoteNotify);
    }

    public function setBillingAddressId($id)
    {
        return parent::setBillingAddressId($id);
    }

    public function setCustomerGroupId($id)
    {
        return parent::setCustomerGroupId($id);
    }

    public function setEditIncrement($editIncrement)
    {
        return parent::setEditIncrement($editIncrement);
    }

    public function setEmailSent($emailSent)
    {
        return parent::setEmailSent($emailSent);
    }

    public function setForcedShipmentWithInvoice($forcedShipmentWithInvoice)
    {
        return parent::setForcedShipmentWithInvoice($forcedShipmentWithInvoice);
    }

    public function setPaymentAuthExpiration($paymentAuthExpiration)
    {
        return parent::setPaymentAuthExpiration($paymentAuthExpiration);
    }

    public function setQuoteAddressId($id)
    {
        return parent::setQuoteAddressId($id);
    }

    public function setQuoteId($id)
    {
        return parent::setQuoteId($id);
    }

    public function setAdjustmentNegative($adjustmentNegative)
    {
        return parent::setAdjustmentNegative($adjustmentNegative);
    }

    public function setAdjustmentPositive($adjustmentPositive)
    {
        return parent::setAdjustmentPositive($adjustmentPositive);
    }

    public function setBaseAdjustmentNegative($baseAdjustmentNegative)
    {
        return parent::setBaseAdjustmentNegative($baseAdjustmentNegative);
    }

    public function setBaseAdjustmentPositive($baseAdjustmentPositive)
    {
        return parent::setBaseAdjustmentPositive($baseAdjustmentPositive);
    }

    public function setBaseShippingDiscountAmount($amount)
    {
        return parent::setBaseShippingDiscountAmount($amount);
    }

    public function setBaseSubtotalInclTax($amount)
    {
        return parent::setBaseSubtotalInclTax($amount);
    }

    public function setBaseTotalDue($baseTotalDue)
    {
        return parent::setBaseTotalDue($baseTotalDue);
    }

    public function setPaymentAuthorizationAmount($amount)
    {
        return parent::setPaymentAuthorizationAmount($amount);
    }

    public function setShippingDiscountAmount($amount)
    {
        return parent::setShippingDiscountAmount($amount);
    }

    public function setSubtotalInclTax($amount)
    {
        return parent::setSubtotalInclTax($amount);
    }

    public function setTotalDue($totalDue)
    {
        return parent::setTotalDue($totalDue);
    }

    public function setWeight($weight)
    {
        return parent::setWeight($weight);
    }

    public function setCustomerDob($customerDob)
    {
        return parent::setCustomerDob($customerDob);
    }

    public function setIncrementId($id)
    {
        return parent::setIncrementId($id);
    }

    public function setAppliedRuleIds($appliedRuleIds)
    {
        return parent::setAppliedRuleIds($appliedRuleIds);
    }

    public function setBaseCurrencyCode($code)
    {
        return parent::setBaseCurrencyCode($code);
    }

    public function setCustomerEmail($customerEmail)
    {
        return parent::setCustomerEmail($customerEmail);
    }

    public function setCustomerFirstname($customerFirstname)
    {
        return parent::setCustomerFirstname($customerFirstname);
    }

    public function setCustomerLastname($customerLastname)
    {
        return parent::setCustomerLastname($customerLastname);
    }

    public function setCustomerMiddlename($customerMiddlename)
    {
        return parent::setCustomerMiddlename($customerMiddlename);
    }

    public function setCustomerPrefix($customerPrefix)
    {
        return parent::setCustomerPrefix($customerPrefix);
    }

    public function setCustomerSuffix($customerSuffix)
    {
        return parent::setCustomerSuffix($customerSuffix);
    }

    public function setCustomerTaxvat($customerTaxvat)
    {
        return parent::setCustomerTaxvat($customerTaxvat);
    }

    public function setDiscountDescription($description)
    {
        return parent::setDiscountDescription($description);
    }

    public function setExtCustomerId($id)
    {
        return parent::setExtCustomerId($id);
    }

    public function setExtOrderId($id)
    {
        return parent::setExtOrderId($id);
    }

    public function setGlobalCurrencyCode($code)
    {
        return parent::setGlobalCurrencyCode($code);
    }

    public function setHoldBeforeState($holdBeforeState)
    {
        return parent::setHoldBeforeState($holdBeforeState);
    }

    public function setHoldBeforeStatus($holdBeforeStatus)
    {
        return parent::setHoldBeforeStatus($holdBeforeStatus);
    }

    public function setOrderCurrencyCode($code)
    {
        return parent::setOrderCurrencyCode($code);
    }

    public function setOriginalIncrementId($id)
    {
        return parent::setOriginalIncrementId($id);
    }

    public function setRelationChildId($id)
    {
        return parent::setRelationChildId($id);
    }

    public function setRelationChildRealId($realId)
    {
        return parent::setRelationChildRealId($realId);
    }

    public function setRelationParentId($id)
    {
        return parent::setRelationParentId($id);
    }

    public function setRelationParentRealId($realId)
    {
        return parent::setRelationParentRealId($realId);
    }

    public function setRemoteIp($remoteIp)
    {
        return parent::setRemoteIp($remoteIp);
    }

    public function setStoreCurrencyCode($code)
    {
        return parent::setStoreCurrencyCode($code);
    }

    public function setStoreName($storeName)
    {
        return parent::setStoreName($storeName);
    }

    public function setXForwardedFor($xForwardedFor)
    {
        return parent::setXForwardedFor($xForwardedFor);
    }

    public function setCustomerNote($customerNote)
    {
        return parent::setCustomerNote($customerNote);
    }

    public function setUpdatedAt($timestamp)
    {
        return parent::setUpdatedAt($timestamp);
    }

    public function setTotalItemCount($totalItemCount)
    {
        return parent::setTotalItemCount($totalItemCount);
    }

    public function setCustomerGender($customerGender)
    {
        return parent::setCustomerGender($customerGender);
    }

    public function setDiscountTaxCompensationAmount($amount)
    {
        return parent::setDiscountTaxCompensationAmount($amount);
    }

    public function setBaseDiscountTaxCompensationAmount($amount)
    {
        return parent::setBaseDiscountTaxCompensationAmount($amount);
    }

    public function setShippingDiscountTaxCompensationAmount($amount)
    {
        return parent::setShippingDiscountTaxCompensationAmount($amount);
    }

    public function setBaseShippingDiscountTaxCompensationAmnt($amnt)
    {
        return parent::setBaseShippingDiscountTaxCompensationAmnt($amnt);
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

    public function setShippingInclTax($amount)
    {
        return parent::setShippingInclTax($amount);
    }

    public function setBaseShippingInclTax($amount)
    {
        return parent::setBaseShippingInclTax($amount);
    }

    public function setShippingMethod($shippingMethod)
    {
        return parent::setShippingMethod($shippingMethod);
    }

    public function getEntityId()
    {
        return $this->_getData('entity_id');
    }

    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }
}
