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

class Table
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
                $item->getName(),
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
                        $child->getName(),
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
        $output = new StreamOutput(fopen('php://stdout', 'w'));
        $table = new SymfonyTable($output);
        $table->setHeaderTitle($title . ' Order' . $order->getEntityId());
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
        $cnt = count($headers);
        $items = $order->getAllVisibleItems() ?: $order->getAllItems();

        $rows = [];
        /** @var OrderItemInterface $item */
        foreach ($items as $item) {
            $rows[] = [
                $item->getItemId(),
                $item->getName(),
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
        $rows[] = new TableSeparator();
        $rows[] = self::totalRowWithTax('ST', $order->getSubtotal(), $order->getSubtotalInclTax(), $cnt);
        $rows[] = self::totalRowWithTax('SH', $order->getShippingAmount(), $order->getShippingInclTax(), $cnt);
        $rows[] = self::totalRowWithTax('DA', $order->getDiscountAmount() ?: 0, $order->getData(DiscountHelperInterface::DA_INCL_TAX), $cnt);
        $rows[] = self::totalRow('GC', $order->getData('gift_cards_amount'), $cnt);
        $rows[] = self::totalRow('RP', $order->getData('reward_currency_amount'), $cnt);
        $rows[] = self::totalRow('CB', $order->getData('customer_balance_amount'), $cnt);
        $rows[] = self::totalRow('Tax', $order->getTaxAmount(), $cnt);
        $rows[] = self::totalRow('GT', $order->getGrandTotal(), $cnt);

        $table->setRows($rows);
        $table->render();
    }

    private static function totalRow($name, $value, $size)
    {
        return [$name, new TableCell($value ?? '', ['colspan' => $size - 1])];
    }

    private static function totalRowWithTax($name, $value, $value2, $size)
    {
        $width = ($size - 2) / 2;

        return [
            $name,
            new TableCell($value ?? '', ['colspan' => $width]),
            'Tax',
            new TableCell($value2 ?? '', ['colspan' => $size - $width - 1]),
        ];
    }
}
