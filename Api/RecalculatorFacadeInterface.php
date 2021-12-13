<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use Magento\Sales\Api\Data\CreditmemoInterface as Creditmemo;
use Magento\Sales\Api\Data\InvoiceInterface as Invoice;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Mygento\Base\Api\Data\RecalculateResultInterface;

interface RecalculatorFacadeInterface
{
    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function execute(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function executeWithoutCalculation(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum.
     * Discounts are spread on all items
     *
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function executeWithSpreading(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum
     * Some items might be split up on 2 items
     *
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function executeWithSplitting(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum
     *
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function executeWithSpreadingAndSplitting(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum. GiftCards is not treated as a discount and not spread to price.
     *
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function executeWithoutGiftCardSpreading(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum. RewardPoints is not treated as a discount and not spread to price.
     *
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function executeWithoutRewardsSpreading(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum. GiftCards, RewardPoints (extra discounts)
     * are not treated as a discounts and not spread to price.
     *
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain attr
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Exception
     * @return array|RecalculateResultInterface with calculated items and sum
     */
    public function executeWithoutExtraDiscountsSpreading(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    );
}
