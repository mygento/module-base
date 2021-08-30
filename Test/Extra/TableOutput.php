<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Extra;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Mygento\Base\Api\DiscountHelperInterface;
use Mygento\Base\Model\Recalculator\Result;
use Symfony\Component\Console\Helper\Table as SymfonyTable;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class Table outputting test data in user-friendly way.
 * Needed for debugging purposes.
 */
class TableOutput
{
    public static function dump($headers, $rows = null)
    {
        if ($rows === null) {
            $rows = $headers;
            $headers = null;
        }
        if (!$rows || count($rows) === 0) {
            dump('no table data');
        }

        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $table = new SymfonyTable($output);

        if ($headers === true) {
            $table->setHeaders(array_keys($rows[0]));
        } elseif (is_array($headers) && count($headers) > 0) {
            $table->setHeaders($headers);
        }

        $table->setRows($rows);
        // writes to output
        $table->render();
    }

    public static function dumpResult(Result $result, string $title = null)
    {
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $table = new SymfonyTable($output);
        $table->setHeaderTitle($title . ' Result');
        $headers = [
            'ID',
            'Name',
            'Price',
            'Qty',
            'Sum',
            'Tax',
            'GC',
            'RP',
            'CB',
            'M',
        ];
        $table->setHeaders($headers);
        $cnt = count($headers);
        $rows = [];
        foreach ($result->getItems() as $key => $item) {
            $r = [
                $key,
                self::cutName($item->getName()),
                $item->getPrice(),
                $item->getQuantity(),
                $item->getSum(),
                $item->getTax(),
                $item->getGiftCardAmount(),
                $item->getRewardCurrencyAmount(),
                $item->getCustomerBalanceAmount(),
                $item->getMarking(),
            ];
            $rows[] = $r;
            if ($item->getChildren()) {
                foreach ($item->getChildren() as $child) {
                    $rows[] = [
                        '|--',
                        self::cutName($child->getName()),
                        $child->getPrice(),
                        $child->getQuantity(),
                        $child->getSum(),
                        $child->getTax(),
                        $child->getGiftCardAmount(),
                        $child->getRewardCurrencyAmount(),
                        $child->getCustomerBalanceAmount(),
                        $child->getMarking(),
                    ];
                }
            }
            $rows[] = new TableSeparator();
        }

        $rows[] = new TableSeparator();
        $rows[] = self::totalRow('GT', $result->getSum(), $cnt);
        $table->setRows($rows);
        $table->render();
    }

    public static function dumpOrder(OrderInterface $order, string $title = null)
    {
        self::showOrderLegend();

        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $table = new SymfonyTable($output);
        $table->setHeaderTitle($title . ' Order. Id: ' . $order->getEntityId());

        $headers = [
            'GT',
            'ST',
            'ST +tax',
            'SH',
            'SH +tax',
            'DA',
            'DA +tax',
            'GC',
            'RP',
            'CB',
            'Tax',
        ];
        $table->setHeaders($headers);

        $dataRow = [
            $order->getGrandTotal(),
            $order->getSubtotal(),
            $order->getSubtotalInclTax(),
            $order->getShippingAmount(),
            $order->getShippingInclTax(),
            $order->getDiscountAmount() ?: 0,
            $order->getData(DiscountHelperInterface::DA_INCL_TAX),
            $order->getData('gift_cards_amount'),
            $order->getData('reward_currency_amount'),
            $order->getData('customer_balance_amount'),
            $order->getTaxAmount(),
        ];

        $order->getGrandTotal() - $order->getShippingInclTax() - $order->getSubtotalInclTax() - $order->getDiscountAmount();

        $table->setRows([$dataRow]);
        $table->render();

        self::dumpOrderItems($order);
    }

    private static function totalRow($name, $value, $size)
    {
        return [$name, new TableCell($value ?? '', ['colspan' => $size - 1])];
    }

    private static function cutName(string $name): string
    {
        $length = 10;
        if (extension_loaded('mbstring')) {
            return mb_strlen($name) > $length
                ? mb_substr($name, 0, $length) . '...'
                : $name;
        }

        return strlen($name) > $length
            ? substr($name, 0, $length) . '...'
            : $name;
    }

    private static function dumpOrderItems(OrderInterface $order): void
    {
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $table = new SymfonyTable($output);
        $table->setHeaderTitle(' Order Items');

        $headers = [
            'ID',
            'Name',
            'Type',
            'Px',
            'Qty',
            'RTx',
            'DA',
            'Total',
            'Tax',
            'Tax %',
            'GC',
            'RP',
            'CB',
        ];
        $table->setHeaders($headers);
        $items = $order->getAllVisibleItems() ?: $order->getAllItems();

        $rows = [];
        /** @var OrderItemInterface $item */
        foreach ($items as $item) {
            $rows[] = [
                $item->getItemId(),
                self::cutName($item->getName()),
                $item->getProductType(),
                $item->getPriceInclTax(),
                $item->getQty(),
                $item->getRowTotalInclTax(),
                $item->getDiscountAmount(),
                $item->getRowTotalInclTax() - $item->getDiscountAmount(),
                $item->getTaxAmount(),
                $item->getTaxPercent(),
                $item->getData('gift_cards_amount'),
                $item->getData('reward_currency_amount'),
                $item->getData('customer_balance_amount'),
            ];
        }

        $table->setRows($rows);
        $table->render();
    }

    private static function showOrderLegend(): void
    {
        $output = new StreamOutput(fopen('php://stdout', 'w'));

        $output->writeln('<info> === LEGEND === </info>');
        $output->write('<info>GT - </info>');
        $output->write('<comment>GrandTotal. </comment>');
        $output->write('<info>ST - </info>');
        $output->write('<comment>SubTotal. </comment>');
        $output->write('<info>ST +tax - </info>');
        $output->write('<comment>SubTotalInclTax. </comment>');
        $output->write('<info>SH - </info>');
        $output->write('<comment>Shipping. </comment>');
        $output->write('<info>DA - </info>');
        $output->write('<comment>DiscountAmount. </comment>');
        $output->write('<info>GC - </info>');
        $output->write('<comment>GiftCardAmount. </comment>');
        $output->write('<info>RP - </info>');
        $output->write('<comment>RewardPointsAmount. </comment>');
        $output->write('<info>CB - </info>');
        $output->write('<comment>CustomerBalanceAmount. </comment>');

        $output->write('<info>Px - </info>');
        $output->write('<comment>PriceInclTax. </comment>');
        $output->write('<info>RTx - </info>');
        $output->writeln('<comment>RowTotalInclTax.</comment>');
    }
}
