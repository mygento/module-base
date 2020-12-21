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
use Mygento\Base\Api\DiscountHelperInterface;
use Mygento\Base\Api\RecalculatorFacadeInterface;
use Mygento\Base\Helper\Discount;
use Mygento\Base\Model\OrderRepository;
use Mygento\Base\Model\Recalculator\ResultFactory;

class RecalculatorFacade implements RecalculatorFacadeInterface
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

    /**
     * @param \Mygento\Base\Helper\Discount $discountHelper
     * @param \Mygento\Base\Model\OrderRepository $orderRepository
     * @param \Mygento\Base\Model\Recalculator\ResultFactory $recalculateResultFactory
     */
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
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        $this->resetHelper();

        return $this->recalculate(...func_get_args());
    }

    /**
     * @inheritDoc
     * @return array|RecalculateResultInterface with calculated items and sum
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        $this->resetHelper();
        $this->discountHelper->setDoCalculation(false);

        return $this->recalculate(...func_get_args());
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        $this->resetHelper();
        $this->discountHelper->setSpreadDiscOnAllUnits(true);

        return $this->recalculate(...func_get_args());
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        $this->resetHelper();
        $this->discountHelper->setIsSplitItemsAllowed(true);

        return $this->recalculate(...func_get_args());
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        $this->resetHelper();
        $this->discountHelper->setIsSplitItemsAllowed(true);
        $this->discountHelper->setSpreadDiscOnAllUnits(true);

        return $this->recalculate(...func_get_args());
    }

    /**
     * @param Creditmemo|Invoice|Order $entity
     * @param mixed $args
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Mygento\Base\Api\Data\RecalculateResultInterface
     */
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
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
                $recalcObject->getItems()[$item->getId()][$extraAmountKey] = $item->getData(
                    DiscountHelperInterface::NAME_NEW_DISC
                );
            }
        }

        $this->resetHelper();

        return $recalcObject;
    }

    /**
     * Reset Discount helper to default state
     */
    protected function resetHelper(): void
    {
        $this->discountHelper->setDoCalculation(self::DO_CALCULATION_DEFAULT_VALUE);
        $this->discountHelper->setIsSplitItemsAllowed(self::IS_SPLIT_ALLOWED_DEFAULT_VALUE);
        $this->discountHelper->setSpreadDiscOnAllUnits(self::SPREAD_DISC_ON_ALL_UNITS_DEFAULT_VALUE);
    }
}
