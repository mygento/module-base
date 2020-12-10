<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service;

use Magento\Sales\Api\Data\CreditmemoInterface as Creditmemo;
use Magento\Sales\Api\Data\InvoiceInterface as Invoice;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Helper\Discount;
use Mygento\Base\Model\OrderRepository;
use Mygento\Base\Model\Recalculator\ResultFactory;

class RecalculatorFacade
{
    private const DO_CALCULATION_DEFAULT_VALUE = true;
    private const IS_SPLIT_ALLOWED_DEFAULT_VALUE = false;
    private const SPREAD_DISC_ON_ALL_UNITS_DEFAULT_VALUE = false;

    /**
     * @var \Mygento\Base\Helper\Discount
     */
    private $discountHelper;

    /**
     * @var \Mygento\Base\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Mygento\Base\Model\Recalculator\ResultFactory
     */
    private $recalculateResultFactory;

    public function __construct(
        Discount $discountHelper,
        OrderRepository $orderRepository,
        ResultFactory $recalculateResultFactory
    ) {
        $this->discountHelper = $discountHelper;
        $this->orderRepository = $orderRepository;
        $this->recalculateResultFactory = $recalculateResultFactory;
    }

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
    ) {
        return $this->recalculate(...func_get_args());
    }

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
    ) {
        $this->discountHelper->setDoCalculation(false);

        return $this->recalculate(...func_get_args());
    }

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
    ) {
        $this->discountHelper->setSpreadDiscOnAllUnits(true);

        return $this->recalculate(...func_get_args());
    }

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
    ) {
        $this->discountHelper->setIsSplitItemsAllowed(true);

        return $this->recalculate(...func_get_args());
    }

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
    ) {
        $this->discountHelper->setIsSplitItemsAllowed(true);
        $this->discountHelper->setSpreadDiscOnAllUnits(true);

        return $this->recalculate(...func_get_args());
    }

    protected function recalculate($entity, ...$args): RecalculateResultInterface
    {
        $res = $this->discountHelper->getRecalculated($entity, ...$args);
        $resultObject = $this->recalculateResultFactory->create($res);

        if ($entity instanceof Order) {
            $this->addExtraDiscounts($entity, $resultObject);
        }

        $this->resetHelper();

        return $resultObject;
    }

    /**
     * @param Order $order
     * @param RecalculateResultInterface $recalcObject
     * @return RecalculateResultInterface
     */
    protected function addExtraDiscounts($order, $recalcObject): RecalculateResultInterface
    {
        $extraAmounts = [
            'reward_currency_amount',
            'gift_cards_amount',
            'customer_balance_amount',
        ];

        $this->discountHelper->setSpreadDiscOnAllUnits(true);

        foreach ($extraAmounts as $extraAmountKey) {
            $extraAmount = $order->getData($extraAmountKey);

            //Do nothing if $extraAmount === 0
            if (bccomp((string) $extraAmount, '0.00', 2) === 0) {
                continue;
            }

            //Clean up the order
            $this->orderRepository->reloadOrder($order->getId());

            $this->discountHelper->applyDiscount($order, (float) $extraAmount);

            foreach ($order->getAllVisibleItems() as $item) {
                $recalcObject->getItems()[$item->getId()][$extraAmountKey] = $item->getData(\Mygento\Base\Helper\Discount::NAME_NEW_DISC);
            }
        }

        $this->resetHelper();

        return $recalcObject;
    }

    protected function resetHelper()
    {
        $this->discountHelper->setDoCalculation(self::DO_CALCULATION_DEFAULT_VALUE);
        $this->discountHelper->setIsSplitItemsAllowed(self::IS_SPLIT_ALLOWED_DEFAULT_VALUE);
        $this->discountHelper->setSpreadDiscOnAllUnits(self::SPREAD_DISC_ON_ALL_UNITS_DEFAULT_VALUE);
    }
}
