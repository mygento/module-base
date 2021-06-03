<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * Class OrderPaymentMock
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderPaymentMock extends DataObject implements OrderPaymentInterface
{
    /**
     * Additional information container
     *
     * @var array
     */
    protected $additionalInformation = [];

    /**
     * Retrieve entity id
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    /**
     * Returns account_status
     *
     * @return string
     */
    public function getAccountStatus()
    {
        return $this->getData(OrderPaymentInterface::ACCOUNT_STATUS);
    }

    /**
     * Returns additional_data
     *
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->getData(OrderPaymentInterface::ADDITIONAL_DATA);
    }

    /**
     * Returns address_status
     *
     * @return string
     */
    public function getAddressStatus()
    {
        return $this->getData(OrderPaymentInterface::ADDRESS_STATUS);
    }

    /**
     * Returns amount_authorized
     *
     * @return float
     */
    public function getAmountAuthorized()
    {
        return $this->getData(OrderPaymentInterface::AMOUNT_AUTHORIZED);
    }

    /**
     * Returns amount_canceled
     *
     * @return float
     */
    public function getAmountCanceled()
    {
        return $this->getData(OrderPaymentInterface::AMOUNT_CANCELED);
    }

    /**
     * Returns amount_ordered
     *
     * @return float
     */
    public function getAmountOrdered()
    {
        return $this->getData(OrderPaymentInterface::AMOUNT_ORDERED);
    }

    /**
     * Returns amount_paid
     *
     * @return float
     */
    public function getAmountPaid()
    {
        return $this->getData(OrderPaymentInterface::AMOUNT_PAID);
    }

    /**
     * Returns amount_refunded
     *
     * @return float
     */
    public function getAmountRefunded()
    {
        return $this->getData(OrderPaymentInterface::AMOUNT_REFUNDED);
    }

    /**
     * Returns anet_trans_method
     *
     * @return string
     */
    public function getAnetTransMethod()
    {
        return $this->getData(OrderPaymentInterface::ANET_TRANS_METHOD);
    }

    /**
     * Returns base_amount_authorized
     *
     * @return float
     */
    public function getBaseAmountAuthorized()
    {
        return $this->getData(OrderPaymentInterface::BASE_AMOUNT_AUTHORIZED);
    }

    /**
     * Returns base_amount_canceled
     *
     * @return float
     */
    public function getBaseAmountCanceled()
    {
        return $this->getData(OrderPaymentInterface::BASE_AMOUNT_CANCELED);
    }

    /**
     * Returns base_amount_ordered
     *
     * @return float
     */
    public function getBaseAmountOrdered()
    {
        return $this->getData(OrderPaymentInterface::BASE_AMOUNT_ORDERED);
    }

    /**
     * Returns base_amount_paid
     *
     * @return float
     */
    public function getBaseAmountPaid()
    {
        return $this->getData(OrderPaymentInterface::BASE_AMOUNT_PAID);
    }

    /**
     * Returns base_amount_paid_online
     *
     * @return float
     */
    public function getBaseAmountPaidOnline()
    {
        return $this->getData(OrderPaymentInterface::BASE_AMOUNT_PAID_ONLINE);
    }

    /**
     * Returns base_amount_refunded
     *
     * @return float
     */
    public function getBaseAmountRefunded()
    {
        return $this->getData(OrderPaymentInterface::BASE_AMOUNT_REFUNDED);
    }

    /**
     * Returns base_amount_refunded_online
     *
     * @return float
     */
    public function getBaseAmountRefundedOnline()
    {
        return $this->getData(OrderPaymentInterface::BASE_AMOUNT_REFUNDED_ONLINE);
    }

    /**
     * Returns base_shipping_amount
     *
     * @return float
     */
    public function getBaseShippingAmount()
    {
        return $this->getData(OrderPaymentInterface::BASE_SHIPPING_AMOUNT);
    }

    /**
     * Returns base_shipping_captured
     *
     * @return float
     */
    public function getBaseShippingCaptured()
    {
        return $this->getData(OrderPaymentInterface::BASE_SHIPPING_CAPTURED);
    }

    /**
     * Returns base_shipping_refunded
     *
     * @return float
     */
    public function getBaseShippingRefunded()
    {
        return $this->getData(OrderPaymentInterface::BASE_SHIPPING_REFUNDED);
    }

    /**
     * Returns cc_approval
     *
     * @return string
     */
    public function getCcApproval()
    {
        return $this->getData(OrderPaymentInterface::CC_APPROVAL);
    }

    /**
     * Returns cc_avs_status
     *
     * @return string
     */
    public function getCcAvsStatus()
    {
        return $this->getData(OrderPaymentInterface::CC_AVS_STATUS);
    }

    /**
     * Returns cc_cid_status
     *
     * @return string
     */
    public function getCcCidStatus()
    {
        return $this->getData(OrderPaymentInterface::CC_CID_STATUS);
    }

    /**
     * Returns cc_debug_request_body
     *
     * @return string
     */
    public function getCcDebugRequestBody()
    {
        return $this->getData(OrderPaymentInterface::CC_DEBUG_REQUEST_BODY);
    }

    /**
     * Returns cc_debug_response_body
     *
     * @return string
     */
    public function getCcDebugResponseBody()
    {
        return $this->getData(OrderPaymentInterface::CC_DEBUG_RESPONSE_BODY);
    }

    /**
     * Returns cc_debug_response_serialized
     *
     * @return string
     */
    public function getCcDebugResponseSerialized()
    {
        return $this->getData(OrderPaymentInterface::CC_DEBUG_RESPONSE_SERIALIZED);
    }

    /**
     * Returns cc_exp_month
     *
     * @return string
     */
    public function getCcExpMonth()
    {
        return $this->getData(OrderPaymentInterface::CC_EXP_MONTH);
    }

    /**
     * Returns cc_exp_year
     *
     * @return string
     */
    public function getCcExpYear()
    {
        return $this->getData(OrderPaymentInterface::CC_EXP_YEAR);
    }

    /**
     * Returns cc_last_4
     *
     * @return string
     */
    public function getCcLast4()
    {
        return $this->getData(OrderPaymentInterface::CC_LAST_4);
    }

    /**
     * Returns cc_number_enc
     *
     * @return string
     */
    public function getCcNumberEnc()
    {
        return $this->getData(OrderPaymentInterface::CC_NUMBER_ENC);
    }

    /**
     * Returns cc_owner
     *
     * @return string
     */
    public function getCcOwner()
    {
        return $this->getData(OrderPaymentInterface::CC_OWNER);
    }

    /**
     * Returns cc_secure_verify
     *
     * @return string
     */
    public function getCcSecureVerify()
    {
        return $this->getData(OrderPaymentInterface::CC_SECURE_VERIFY);
    }

    /**
     * Returns cc_ss_issue
     *
     * @return string
     * @deprecated 100.1.0 unused
     */
    public function getCcSsIssue()
    {
        return $this->getData(OrderPaymentInterface::CC_SS_ISSUE);
    }

    /**
     * Returns cc_ss_start_month
     *
     * @return string
     * @deprecated 100.1.0 unused
     */
    public function getCcSsStartMonth()
    {
        return $this->getData(OrderPaymentInterface::CC_SS_START_MONTH);
    }

    /**
     * Returns cc_ss_start_year
     *
     * @return string
     * @deprecated 100.1.0 unused
     */
    public function getCcSsStartYear()
    {
        return $this->getData(OrderPaymentInterface::CC_SS_START_YEAR);
    }

    /**
     * Returns cc_status
     *
     * @return string
     */
    public function getCcStatus()
    {
        return $this->getData(OrderPaymentInterface::CC_STATUS);
    }

    /**
     * Returns cc_status_description
     *
     * @return string
     */
    public function getCcStatusDescription()
    {
        return $this->getData(OrderPaymentInterface::CC_STATUS_DESCRIPTION);
    }

    /**
     * Returns cc_trans_id
     *
     * @return string
     */
    public function getCcTransId()
    {
        return $this->getData(OrderPaymentInterface::CC_TRANS_ID);
    }

    /**
     * Returns cc_type
     *
     * @return string
     */
    public function getCcType()
    {
        return $this->getData(OrderPaymentInterface::CC_TYPE);
    }

    /**
     * Returns echeck_account_name
     *
     * @return string
     */
    public function getEcheckAccountName()
    {
        return $this->getData(OrderPaymentInterface::ECHECK_ACCOUNT_NAME);
    }

    /**
     * Returns echeck_account_type
     *
     * @return string
     */
    public function getEcheckAccountType()
    {
        return $this->getData(OrderPaymentInterface::ECHECK_ACCOUNT_TYPE);
    }

    /**
     * Returns echeck_bank_name
     *
     * @return string
     */
    public function getEcheckBankName()
    {
        return $this->getData(OrderPaymentInterface::ECHECK_BANK_NAME);
    }

    /**
     * Returns echeck_routing_number
     *
     * @return string
     */
    public function getEcheckRoutingNumber()
    {
        return $this->getData(OrderPaymentInterface::ECHECK_ROUTING_NUMBER);
    }

    /**
     * Returns echeck_type
     *
     * @return string
     */
    public function getEcheckType()
    {
        return $this->getData(OrderPaymentInterface::ECHECK_TYPE);
    }

    /**
     * Returns last_trans_id
     *
     * @return string
     */
    public function getLastTransId()
    {
        return $this->getData(OrderPaymentInterface::LAST_TRANS_ID);
    }

    /**
     * Returns method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getData(OrderPaymentInterface::METHOD);
    }

    /**
     * Returns parent_id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->getData(OrderPaymentInterface::PARENT_ID);
    }

    /**
     * Returns po_number
     *
     * @return string
     */
    public function getPoNumber()
    {
        return $this->getData(OrderPaymentInterface::PO_NUMBER);
    }

    /**
     * Returns protection_eligibility
     *
     * @return string
     */
    public function getProtectionEligibility()
    {
        return $this->getData(OrderPaymentInterface::PROTECTION_ELIGIBILITY);
    }

    /**
     * Returns quote_payment_id
     *
     * @return int
     */
    public function getQuotePaymentId()
    {
        return $this->getData(OrderPaymentInterface::QUOTE_PAYMENT_ID);
    }

    /**
     * Returns shipping_amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return $this->getData(OrderPaymentInterface::SHIPPING_AMOUNT);
    }

    /**
     * Returns shipping_captured
     *
     * @return float
     */
    public function getShippingCaptured()
    {
        return $this->getData(OrderPaymentInterface::SHIPPING_CAPTURED);
    }

    /**
     * Returns shipping_refunded
     *
     * @return float
     */
    public function getShippingRefunded()
    {
        return $this->getData(OrderPaymentInterface::SHIPPING_REFUNDED);
    }

    /**
     * @inheritdoc
     */
    public function setParentId($id)
    {
        return $this->setData(OrderPaymentInterface::PARENT_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingCaptured($baseShippingCaptured)
    {
        return $this->setData(OrderPaymentInterface::BASE_SHIPPING_CAPTURED, $baseShippingCaptured);
    }

    /**
     * @inheritdoc
     */
    public function setShippingCaptured($shippingCaptured)
    {
        return $this->setData(OrderPaymentInterface::SHIPPING_CAPTURED, $shippingCaptured);
    }

    /**
     * @inheritdoc
     */
    public function setAmountRefunded($amountRefunded)
    {
        return $this->setData(OrderPaymentInterface::AMOUNT_REFUNDED, $amountRefunded);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountPaid($baseAmountPaid)
    {
        return $this->setData(OrderPaymentInterface::BASE_AMOUNT_PAID, $baseAmountPaid);
    }

    /**
     * @inheritdoc
     */
    public function setAmountCanceled($amountCanceled)
    {
        return $this->setData(OrderPaymentInterface::AMOUNT_CANCELED, $amountCanceled);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountAuthorized($baseAmountAuthorized)
    {
        return $this->setData(OrderPaymentInterface::BASE_AMOUNT_AUTHORIZED, $baseAmountAuthorized);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountPaidOnline($baseAmountPaidOnline)
    {
        return $this->setData(OrderPaymentInterface::BASE_AMOUNT_PAID_ONLINE, $baseAmountPaidOnline);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountRefundedOnline($baseAmountRefundedOnline)
    {
        return $this->setData(OrderPaymentInterface::BASE_AMOUNT_REFUNDED_ONLINE, $baseAmountRefundedOnline);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingAmount($amount)
    {
        return $this->setData(OrderPaymentInterface::BASE_SHIPPING_AMOUNT, $amount);
    }

    /**
     * @inheritdoc
     */
    public function setShippingAmount($amount)
    {
        return $this->setData(OrderPaymentInterface::SHIPPING_AMOUNT, $amount);
    }

    /**
     * @inheritdoc
     */
    public function setAmountPaid($amountPaid)
    {
        return $this->setData(OrderPaymentInterface::AMOUNT_PAID, $amountPaid);
    }

    /**
     * @inheritdoc
     */
    public function setAmountAuthorized($amountAuthorized)
    {
        return $this->setData(OrderPaymentInterface::AMOUNT_AUTHORIZED, $amountAuthorized);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountOrdered($baseAmountOrdered)
    {
        return $this->setData(OrderPaymentInterface::BASE_AMOUNT_ORDERED, $baseAmountOrdered);
    }

    /**
     * @inheritdoc
     */
    public function setBaseShippingRefunded($baseShippingRefunded)
    {
        return $this->setData(OrderPaymentInterface::BASE_SHIPPING_REFUNDED, $baseShippingRefunded);
    }

    /**
     * @inheritdoc
     */
    public function setShippingRefunded($shippingRefunded)
    {
        return $this->setData(OrderPaymentInterface::SHIPPING_REFUNDED, $shippingRefunded);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountRefunded($baseAmountRefunded)
    {
        return $this->setData(OrderPaymentInterface::BASE_AMOUNT_REFUNDED, $baseAmountRefunded);
    }

    /**
     * @inheritdoc
     */
    public function setAmountOrdered($amountOrdered)
    {
        return $this->setData(OrderPaymentInterface::AMOUNT_ORDERED, $amountOrdered);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountCanceled($baseAmountCanceled)
    {
        return $this->setData(OrderPaymentInterface::BASE_AMOUNT_CANCELED, $baseAmountCanceled);
    }

    /**
     * @inheritdoc
     */
    public function setQuotePaymentId($id)
    {
        return $this->setData(OrderPaymentInterface::QUOTE_PAYMENT_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function setAdditionalData($additionalData)
    {
        return $this->setData(OrderPaymentInterface::ADDITIONAL_DATA, $additionalData);
    }

    /**
     * @inheritdoc
     */
    public function setCcExpMonth($ccExpMonth)
    {
        return $this->setData(OrderPaymentInterface::CC_EXP_MONTH, $ccExpMonth);
    }

    /**
     * @inheritdoc
     * @deprecated 100.1.0 unused
     */
    public function setCcSsStartYear($ccSsStartYear)
    {
        return $this->setData(OrderPaymentInterface::CC_SS_START_YEAR, $ccSsStartYear);
    }

    /**
     * @inheritdoc
     */
    public function setEcheckBankName($echeckBankName)
    {
        return $this->setData(OrderPaymentInterface::ECHECK_BANK_NAME, $echeckBankName);
    }

    /**
     * @inheritdoc
     */
    public function setMethod($method)
    {
        return $this->setData(OrderPaymentInterface::METHOD, $method);
    }

    /**
     * @inheritdoc
     */
    public function setCcDebugRequestBody($ccDebugRequestBody)
    {
        return $this->setData(OrderPaymentInterface::CC_DEBUG_REQUEST_BODY, $ccDebugRequestBody);
    }

    /**
     * @inheritdoc
     */
    public function setCcSecureVerify($ccSecureVerify)
    {
        return $this->setData(OrderPaymentInterface::CC_SECURE_VERIFY, $ccSecureVerify);
    }

    /**
     * @inheritdoc
     */
    public function setProtectionEligibility($protectionEligibility)
    {
        return $this->setData(OrderPaymentInterface::PROTECTION_ELIGIBILITY, $protectionEligibility);
    }

    /**
     * @inheritdoc
     */
    public function setCcApproval($ccApproval)
    {
        return $this->setData(OrderPaymentInterface::CC_APPROVAL, $ccApproval);
    }

    /**
     * @inheritdoc
     */
    public function setCcLast4($ccLast4)
    {
        return $this->setData(OrderPaymentInterface::CC_LAST_4, $ccLast4);
    }

    /**
     * @inheritdoc
     */
    public function setCcStatusDescription($description)
    {
        return $this->setData(OrderPaymentInterface::CC_STATUS_DESCRIPTION, $description);
    }

    /**
     * @inheritdoc
     */
    public function setEcheckType($echeckType)
    {
        return $this->setData(OrderPaymentInterface::ECHECK_TYPE, $echeckType);
    }

    /**
     * @inheritdoc
     */
    public function setCcDebugResponseSerialized($ccDebugResponseSerialized)
    {
        return $this->setData(OrderPaymentInterface::CC_DEBUG_RESPONSE_SERIALIZED, $ccDebugResponseSerialized);
    }

    /**
     * @inheritdoc
     * @deprecated 100.1.0 unused
     */
    public function setCcSsStartMonth($ccSsStartMonth)
    {
        return $this->setData(OrderPaymentInterface::CC_SS_START_MONTH, $ccSsStartMonth);
    }

    /**
     * @inheritdoc
     */
    public function setEcheckAccountType($echeckAccountType)
    {
        return $this->setData(OrderPaymentInterface::ECHECK_ACCOUNT_TYPE, $echeckAccountType);
    }

    /**
     * @inheritdoc
     */
    public function setLastTransId($id)
    {
        return $this->setData(OrderPaymentInterface::LAST_TRANS_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function setCcCidStatus($ccCidStatus)
    {
        return $this->setData(OrderPaymentInterface::CC_CID_STATUS, $ccCidStatus);
    }

    /**
     * @inheritdoc
     */
    public function setCcOwner($ccOwner)
    {
        return $this->setData(OrderPaymentInterface::CC_OWNER, $ccOwner);
    }

    /**
     * @inheritdoc
     */
    public function setCcType($ccType)
    {
        return $this->setData(OrderPaymentInterface::CC_TYPE, $ccType);
    }

    /**
     * @inheritdoc
     */
    public function setPoNumber($poNumber)
    {
        return $this->setData(OrderPaymentInterface::PO_NUMBER, $poNumber);
    }

    /**
     * @inheritdoc
     */
    public function setCcExpYear($ccExpYear)
    {
        return $this->setData(OrderPaymentInterface::CC_EXP_YEAR, $ccExpYear);
    }

    /**
     * @inheritdoc
     */
    public function setCcStatus($ccStatus)
    {
        return $this->setData(OrderPaymentInterface::CC_STATUS, $ccStatus);
    }

    /**
     * @inheritdoc
     */
    public function setEcheckRoutingNumber($echeckRoutingNumber)
    {
        return $this->setData(OrderPaymentInterface::ECHECK_ROUTING_NUMBER, $echeckRoutingNumber);
    }

    /**
     * @inheritdoc
     */
    public function setAccountStatus($accountStatus)
    {
        return $this->setData(OrderPaymentInterface::ACCOUNT_STATUS, $accountStatus);
    }

    /**
     * @inheritdoc
     */
    public function setAnetTransMethod($anetTransMethod)
    {
        return $this->setData(OrderPaymentInterface::ANET_TRANS_METHOD, $anetTransMethod);
    }

    /**
     * @inheritdoc
     */
    public function setCcDebugResponseBody($ccDebugResponseBody)
    {
        return $this->setData(OrderPaymentInterface::CC_DEBUG_RESPONSE_BODY, $ccDebugResponseBody);
    }

    /**
     * @inheritdoc
     * @deprecated 100.1.0 unused
     */
    public function setCcSsIssue($ccSsIssue)
    {
        return $this->setData(OrderPaymentInterface::CC_SS_ISSUE, $ccSsIssue);
    }

    /**
     * @inheritdoc
     */
    public function setEcheckAccountName($echeckAccountName)
    {
        return $this->setData(OrderPaymentInterface::ECHECK_ACCOUNT_NAME, $echeckAccountName);
    }

    /**
     * @inheritdoc
     */
    public function setCcAvsStatus($ccAvsStatus)
    {
        return $this->setData(OrderPaymentInterface::CC_AVS_STATUS, $ccAvsStatus);
    }

    /**
     * @inheritdoc
     */
    public function setCcNumberEnc($ccNumberEnc)
    {
        return $this->setData(OrderPaymentInterface::CC_NUMBER_ENC, $ccNumberEnc);
    }

    /**
     * @inheritdoc
     */
    public function setCcTransId($id)
    {
        return $this->setData(OrderPaymentInterface::CC_TRANS_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function setAddressStatus($addressStatus)
    {
        return $this->setData(OrderPaymentInterface::ADDRESS_STATUS, $addressStatus);
    }

    /**
     * @inheritdoc
     *
     * @return \Magento\Sales\Api\Data\OrderPaymentExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->getData(ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Sales\Api\Data\OrderPaymentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(OrderPaymentExtensionInterface $extensionAttributes)
    {
        $this->setData(ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);

        return $this;
    }

    /**
     * Getter for entire additional_information value or one of its element by key
     *
     * @param string $key
     * @return array|mixed|null
     */
    public function getAdditionalInformation($key = null)
    {
        $this->initAdditionalInformation();
        if (null === $key) {
            return $this->additionalInformation;
        }

        return $this->additionalInformation[$key] ?? null;
    }

    /**
     * Additional information setter
     * Updates data inside the 'additional_information' array
     * or all 'additional_information' if key is data array
     *
     * @param array|string $key
     * @param mixed $value
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function setAdditionalInformation($key, $value = null)
    {
        if (is_object($value)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The payment disallows storing objects.'));
        }
        $this->initAdditionalInformation();
        if (is_array($key) && $value === null) {
            $this->additionalInformation = $key;
        } else {
            $this->additionalInformation[$key] = $value;
        }

        return $this->setData('additional_information', $this->additionalInformation);
    }

    /**
     * Check whether there is additional information by specified key
     *
     * @param mixed|null $key
     * @return bool
     */
    public function hasAdditionalInformation($key = null)
    {
        $this->initAdditionalInformation();

        return null === $key ? !empty($this->additionalInformation) : array_key_exists(
            $key,
            $this->additionalInformation
        );
    }

    /**
     * Initialize additional information container with data from model if property empty
     *
     * @return void
     */
    protected function initAdditionalInformation()
    {
        $additionalInfo = $this->getData('additional_information');
        if (empty($this->additionalInformation) && $additionalInfo) {
            $this->additionalInformation = $additionalInfo;
        }
    }
}
