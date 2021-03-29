<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service;

use Magento\Sales\Api\Data\CreditmemoInterface as Creditmemo;
use Magento\Sales\Api\Data\InvoiceInterface as Invoice;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\DiscountHelperInterface as Discount;
use Mygento\Base\Api\RecalculatorFacadeInterface;
use Mygento\Base\Model\Recalculator\ResultFactory;

class RecalculatorFacade implements RecalculatorFacadeInterface
{
    private const DO_CALCULATION_DEFAULT_VALUE = true;
    private const IS_SPLIT_ALLOWED_DEFAULT_VALUE = false;
    private const SPREAD_DISC_ON_ALL_UNITS_DEFAULT_VALUE = false;

    /**
     * @var \Mygento\Base\Api\DiscountHelperInterface
     */
    private $discountHelper;

    /**
     * @var \Mygento\Base\Model\Recalculator\ResultFactory
     */
    private $recalculateResultFactory;

    /**
     * @var \Mygento\Base\Api\RecalculationHandler[]
     */
    private $handlers;

    /**
     * @param \Mygento\Base\Api\DiscountHelperInterface $discountHelper
     * @param \Mygento\Base\Model\Recalculator\ResultFactory $recalculateResultFactory
     */
    public function __construct(
        Discount $discountHelper,
        ResultFactory $recalculateResultFactory,
        array $handlers = []
    ) {
        $this->discountHelper = $discountHelper;
        $this->recalculateResultFactory = $recalculateResultFactory;
        $this->handlers = $handlers;
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

        //Apply POST Handlers
        if ($entity instanceof Order) {
            //Make some auxiliary actions after recalculation
            foreach ($this->handlers as $handler) {
                $handler->handle($entity, $resultObject);
            }
        }

        $this->resetHelper();

        return $resultObject;
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
