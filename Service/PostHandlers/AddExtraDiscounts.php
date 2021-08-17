<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PostHandlers;

use Magento\Sales\Api\Data\OrderInterface as Order;
use Mygento\Base\Api\Data\PaymentInterface;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\DiscountHelperInterface;
use Mygento\Base\Api\DiscountHelperInterfaceFactory;
use Mygento\Base\Api\RecalculationPostHandlerInterface;
use Mygento\Base\Model\OrderRepository;

class AddExtraDiscounts implements RecalculationPostHandlerInterface
{
    /**
     * @var \Mygento\Base\Api\DiscountHelperInterfaceFactory
     */
    private $discountHelperFactory;

    /**
     * @var \Mygento\Base\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @param \Mygento\Base\Api\DiscountHelperInterfaceFactory $discountHelperFactory
     * @param \Mygento\Base\Model\OrderRepository $orderRepository
     */
    public function __construct(
        DiscountHelperInterfaceFactory $discountHelperFactory,
        OrderRepository $orderRepository
    ) {
        $this->discountHelperFactory = $discountHelperFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Order $order
     * @param RecalculateResultInterface $recalcOriginal
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return RecalculateResultInterface
     */
    public function handle(Order $order, RecalculateResultInterface $recalcOriginal): RecalculateResultInterface
    {
        /** @var \Mygento\Base\Api\DiscountHelperInterface $discountHelper */
        $discountHelper = $this->discountHelperFactory->create();

        $extraAmounts = [
            'reward_currency_amount',
            'gift_cards_amount',
            'customer_balance_amount',
        ];

        $discountHelper->setSpreadDiscOnAllUnits(true);

        $isRecalculated = $order->getPayment()->getAdditionalInformation(PaymentInterface::RECALCULATED_FLAG);

        foreach ($extraAmounts as $extraAmountKey) {
            $extraAmount = $order->getData($extraAmountKey);

            //Do nothing if $extraAmount === 0
            if (bccomp((string) $extraAmount, '0.00', 2) === 0) {
                continue;
            }

            //Clean up the order
            $this->orderRepository->reloadOrder($order->getId());

            if (!$isRecalculated) {
                $discountHelper->applyDiscount($order, (float) $extraAmount);
            }

            $sourceAmountKey = $isRecalculated ? $extraAmountKey : DiscountHelperInterface::NAME_ROW_AMOUNT_TO_SPREAD;
            foreach ($order->getAllVisibleItems() as $item) {
                $recalcOriginal->getItemById($item->getId())[$extraAmountKey] = $item->getData($sourceAmountKey);
            }
        }

        return $recalcOriginal;
    }
}
