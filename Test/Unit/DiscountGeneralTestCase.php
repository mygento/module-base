<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit;

use PHPUnit\Framework\TestCase;

class DiscountGeneralTestCase extends TestCase
{
    //consts for getRecalculated() method
    const TEST_CASE_NAME_1 = '#case 1. Скидки только на товары. Все делится нацело. Bug 1 kop. Товары по 1 шт. Два со скидками, а один бесплатный.';
    const TEST_CASE_NAME_2 = '#case 2. Скидка на весь чек и на отдельный товар. Order 145000128 DemoEE';
    const TEST_CASE_NAME_3 = '#case 3. Скидка на каждый товар.';
    const TEST_CASE_NAME_4 = '#case 4. Нет скидок никаких';
    const TEST_CASE_NAME_5 = '#case 5. Скидки только на товары. Не делятся нацело.';
    const TEST_CASE_NAME_6 = '#case 6. Есть позиция, на которую НЕ ДОЛЖНА распространиться скидка.';
    const TEST_CASE_NAME_7 = '#case 7. Bug grandTotal < чем сумма всех позиций. Есть позиция со 100% скидкой.';
    const TEST_CASE_NAME_8 = '#case 8. Reward points в заказе. 1 товар со скидкой, 1 без';
    const TEST_CASE_NAME_9 = '#case 9. Reward points в заказе. В заказе только 1 товар и тот без скидки';
    const TEST_CASE_NAME_10 = '#case 10. Reward points в заказе. На товары нет скидок';
    const TEST_CASE_NAME_11 = '#case 11. (prd nn) order 100374806';
    const TEST_CASE_NAME_12 = '#case 12. invoice NN 100057070. Неверно расчитан grandTotal в Magento';
    const TEST_CASE_NAME_13 = '#case 13. Такой же как и invoice NN 100057070, но без большого продукта. Неверно расчитан grandTotal в Magento';
    const TEST_CASE_NAME_14 = '#case 14. Тест 1 на мелкие rewardPoints (0.01)';
    const TEST_CASE_NAME_15 = '#case 15. Тест 2 на мелкие rewardPoints (0.31)';
    const TEST_CASE_NAME_16 = '#case 16. Тест 1 на мелкие rewardPoints (9.99)';
    const TEST_CASE_NAME_17 = '#case 17. гипотетическая ситуация с ошибкой расчета Мagento -1 коп.';
    const TEST_CASE_NAME_18 = '#case 18. Подарочная карта. Полная оплата';
    const TEST_CASE_NAME_19 = '#case 19. Store Credit. Частичная оплата';
    const TEST_CASE_NAME_20 = '#case 20. Bug with negative Qty';
    const TEST_CASE_NAME_21 = '#case 21. Bug with negative Price because of converting float -28.9999999999999 to int  (e.g. invoice 100091106)';
    const TEST_CASE_NAME_22 = '#case 22. Bug with taxes. Когда настроено налоговое правило и цены в каталоге без налога. Скидка не содержит налог';
    const TEST_CASE_NAME_23 = '#case 23. Bug with taxes. Есть налоговое правило. Налог применяется до скидки, а скидка применяется на цены, содержащие налог. То есть скидка содержит налог.';
    const TEST_CASE_NAME_24 = '#case 24. Bug with shipping discount. Доставка со скидкой 100%. Настройки налогов как в #23';
    const TEST_CASE_NAME_25 = '#case 25. Баг с отрицательной суммой кофе-машины (макс цена в заказе) и отрицательной суммы доставки';

    const CHARS_LOWERS = 'abcdefghijklmnopqrstuvwxyz';
    const CHARS_UPPERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHARS_DIGITS = '0123456789';
    const CHARS_SPECIALS = '!$*+-.=?@^_|~';

    /**
     * @var \Mygento\Base\Helper\Discount
     */
    protected $discountHelper = null;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectMan;

    protected function setUp()
    {
        $this->discountHelper = $this->getDiscountHelperInstance();
    }

    /**
     * @return \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    public function getObjectManager(): \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
    {
        if (!$this->objectMan) {
            $this->objectMan = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager(
                $this
            );
        }

        return $this->objectMan;
    }

    public function getDiscountHelperInstance()
    {
        $this->discountHelper = $this->getObjectManager()->getObject(
            \Mygento\Base\Helper\Discount::class
        );

        return $this->discountHelper;
    }

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @param mixed $order
     * @param mixed $expectedArray
     */
    public function testCalculation($order, $expectedArray)
    {
        //В случае если добавили новый тест и у него еще нет expectedArray - то выводим его с соотв. округлением значений
        if (is_null($expectedArray)) {
            echo "\033[1;32m"; // green
            echo $this->getName() . PHP_EOL;
            echo "\033[1;33m"; // yellow
            $storedValue = ini_get('serialize_precision');
            ini_set('serialize_precision', 12);
            var_export($this->discountHelper->getRecalculated($order, 'vat20'));
            ini_set('serialize_precision', $storedValue);
            echo "\033[0m"; // reset color
            exit();
        }
    }

    /**
     * @param float $rowTotalInclTax
     * @param float $priceInclTax
     * @param float $discountAmount
     * @param int $qty
     * @param int $taxPercent
     * @param float|int $taxAmount
     * @return \Mygento\Base\Test\OrderItemMock
     */
    public function getItem(
        $rowTotalInclTax,
        $priceInclTax,
        $discountAmount,
        $qty = 1,
        $taxPercent = 0,
        $taxAmount = 0
    ) {
        static $id = 100500;
        $id++;

        $name = $this->getRandomString(8);

        $item = $this->getObjectManager()->getObject(
            \Mygento\Base\Test\OrderItemMock::class
        );

        $item->setData('id', $id);
        $item->setData('row_total_incl_tax', $rowTotalInclTax);
        $item->setData('price_incl_tax', $priceInclTax);
        $item->setData('discount_amount', $discountAmount);
        $item->setData('qty', $qty);
        $item->setData('name', $name);
        $item->setData('tax_percent', $taxPercent);
        $item->setData('tax_amount', $taxAmount);

        return $item;
    }

    public function addItem($order, $item)
    {
        $items = (array) $order->getData('all_items');
        $items[] = $item;

        $order->setData('all_items', $items);
    }

    public function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }

        return $str;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function dataProviderOrdersForCheckCalculation()
    {
        $final = [];

        //Тест кейсы одинаковые для всех вариантов настроек класса Discount
        $orders = self::getOrders();
        //А ожидаемые результаты должны быть в каждом классе свои
        $expected = static::getExpected();

        foreach ($orders as $key => $order) {
            $final[$key] = [$order, $expected[$key]];
        }

        return $final;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function getOrders()
    {
        $final = [];

        //Bug 1 kop. Товары по 1 шт. Два со скидками, а один бесплатный. Цены делятся нацело - пересчет не должен применяться.
        $order = $this->getNewOrderInstance(13380.0000, 12069.3000, 0.0000);
        $this->addItem($order, $this->getItem(12990.0000, 12990.0000, 1299.0000));
        $this->addItem($order, $this->getItem(390.0000, 390.0000, 11.7000));
        $this->addItem($order, $this->getItem(0.0000, 0.0000, 0.0000));
        $final[self::TEST_CASE_NAME_1] = $order;

        $order = $this->getNewOrderInstance(5125.8600, 9373.1900, 4287.00);
        $this->addItem($order, $this->getItem(5054.4000, 5054.4000, 0.0000));
        $this->addItem($order, $this->getItem(71.4600, 23.8200, 39.6700, 3));
        $final[self::TEST_CASE_NAME_2] = $order;

        //39.67 Discount на весь заказ. Magento размазывает по товарам скидку в заказе.
        $order = $this->getNewOrderInstance(5125.8600, 5106.1900, 20.00);
        $this->addItem($order, $this->getItem(5054.4000, 5054.4000, 39.1200));
        $this->addItem($order, $this->getItem(71.4600, 23.8200, 0.5500, 3));
        $final[self::TEST_CASE_NAME_3] = $order;

        $order = $this->getNewOrderInstance(5000.8600, 5200.8600, 200.00);
        $this->addItem($order, $this->getItem(1000.8200, 500.4100, 0.0000, 2));
        $this->addItem($order, $this->getItem(4000.04, 1000.01, 0.0000, 4));
        $final[self::TEST_CASE_NAME_4] = $order;

        $order = $this->getNewOrderInstance(222, 202.1, 0);
        $this->addItem($order, $this->getItem(120, 40, 19, 3));
        $this->addItem($order, $this->getItem(102, 25.5, 0.9, 4));
        $final[self::TEST_CASE_NAME_5] = $order;

        $order = $this->getNewOrderInstance(722, 702.1, 0);
        $this->addItem($order, $this->getItem(120, 40, 19, 3));
        $this->addItem($order, $this->getItem(102, 25.5, 0.9, 4));
        $this->addItem($order, $this->getItem(500, 100, 0, 5));
        $final[self::TEST_CASE_NAME_6] = $order;

        //Bug GrandTotal заказа меньше, чем сумма всех позиций. На 1 товар скидка 100%
        $order = $this->getNewOrderInstance(13010.0000, 11691.0000, 0);
        $this->addItem($order, $this->getItem(12990.0000, 12990.0000, 1299.0000, 1));
        $this->addItem($order, $this->getItem(20.0000, 20.0000, 20.0000, 1));
        $final[self::TEST_CASE_NAME_7] = $order;

        //Reward Points included
        $order = $this->getNewOrderInstance(13010.0000, 11611.0000, 0, 100);
        $this->addItem($order, $this->getItem(12990.0000, 12990.0000, 1299.0000, 1));
        $this->addItem($order, $this->getItem(20.0000, 20.0000, 0.0000, 1));
        $final[self::TEST_CASE_NAME_8] = $order;

        //Reward Points included 2
        $order = $this->getNewOrderInstance(12990.0000, 12890.0000, 0, 100);
        $this->addItem($order, $this->getItem(12990.0000, 12990.0000, 0.0000, 1));
        $final[self::TEST_CASE_NAME_9] = $order;

        //Reward Points included 3
        $order = $this->getNewOrderInstance(13010.0000, 12909.9900, 0, 100.01);
        $this->addItem($order, $this->getItem(12990.0000, 12990.0000, 0.0000, 1));
        $this->addItem($order, $this->getItem(20.0000, 20.0000, 0.0000, 1));
        $final[self::TEST_CASE_NAME_10] = $order;

        //Very bad order 100374806 (prd nn)
        $order = $this->getNewOrderInstance(37130.0100, 32130.0100, 0);
        $this->addItem($order, $this->getItem(19990.0000, 19990.0000, 0.0000, 1));
        $this->addItem($order, $this->getItem(14500.0000, 29.0000, 5000.0000, 500));
        $this->addItem($order, $this->getItem(1000.0100, 1000.0000, 0.0000, 1));
        $this->addItem($order, $this->getItem(1640.0000, 410.0000, 0.0000, 4));
        $final[self::TEST_CASE_NAME_11] = $order;

        //Ошибка в расчете Magento. -1 коп уходили в доставку
        $order = $this->getNewOrderInstance(18189.9900, 13189.9900, 0.0000, 0);
        $this->addItem($order, $this->getItem(7990.0000, 7990.0000, 0, 1.0000));
        $this->addItem($order, $this->getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        $this->addItem($order, $this->getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(360.0000, 36.0000, 176.4700, 10.0000));
        $this->addItem($order, $this->getItem(1800.00, 36.00, 882.35, 50.00));
        $this->addItem($order, $this->getItem(330.0000, 33.0000, 161.7600, 10.0000));
        $this->addItem($order, $this->getItem(720.0000, 36.0000, 352.9400, 20.0000));
        $this->addItem($order, $this->getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_12] = $order;

        //Ошибка в расчете Magento. -1 коп уходили в доставку. Без большого продукта
        $order = $this->getNewOrderInstance(10199.9900, 5199.9900, 0.0000, 0.00);
        $this->addItem($order, $this->getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        $this->addItem($order, $this->getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(360.0000, 36.0000, 176.4700, 10.0000));
        $this->addItem($order, $this->getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        $this->addItem($order, $this->getItem(330.0000, 33.0000, 161.7600, 10.0000));
        $this->addItem($order, $this->getItem(720.0000, 36.0000, 352.9400, 20.0000));
        $this->addItem($order, $this->getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_13] = $order;

        //Такой же как и предыдущий, только копейка в +
        $order = $this->getNewOrderInstance(18190.0100, 13190.0100, 0.0000, 0);
        $this->addItem($order, $this->getItem(7990.0000, 7990.0000, 0, 1.0000));
        $this->addItem($order, $this->getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        $this->addItem($order, $this->getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(360.0000, 36.0000, 176.4700, 10.0000));
        $this->addItem($order, $this->getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        $this->addItem($order, $this->getItem(330.0000, 33.0000, 161.7600, 10.0000));
        $this->addItem($order, $this->getItem(720.0000, 36.0000, 352.9400, 20.0000));
        $this->addItem($order, $this->getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_14] = $order;

        //тестируем размазывание мелких ревардов
        $order = $this->getNewOrderInstance(18190.0000, 13189.6900, 0.0000, 0.31);
        $this->addItem($order, $this->getItem(7990.0000, 7990.0000, 0, 1.0000));
        $this->addItem($order, $this->getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        $this->addItem($order, $this->getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(360.0000, 36.0000, 176.4700, 10.0000));
        $this->addItem($order, $this->getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        $this->addItem($order, $this->getItem(330.0000, 33.0000, 161.7600, 10.0000));
        $this->addItem($order, $this->getItem(720.0000, 36.0000, 352.9400, 20.0000));
        $this->addItem($order, $this->getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_15] = $order;

        //тестируем размазывание мелких ревардов
        $order = $this->getNewOrderInstance(10200, 5190.0100, 0.0000, 9.99);
        $this->addItem($order, $this->getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        $this->addItem($order, $this->getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        $this->addItem($order, $this->getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        $this->addItem($order, $this->getItem(360.0000, 36.0000, 176.4700, 10.0000));
        $this->addItem($order, $this->getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        $this->addItem($order, $this->getItem(330.0000, 33.0000, 161.7600, 10.0000));
        $this->addItem($order, $this->getItem(720.0000, 36.0000, 352.9400, 20.0000));
        $this->addItem($order, $this->getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_16] = $order;

        //Случай совсем из фантастики
        $order = $this->getNewOrderInstance(12989.9900, 7989.9900, 0.0000, 0);
        $this->addItem($order, $this->getItem(5000.0000, 50.0000, 5000.0000, 100.0000));
        $this->addItem($order, $this->getItem(7990.0000, 7990.0000, 0, 1.0000));
        $final[self::TEST_CASE_NAME_17] = $order;

        //Оплата Подарочной картой или Store Credit - должна в налоговую быть предоставленой как обычное поступление средств
        //Gift Card - полная оплата
        $order = $this->getNewOrderInstance(1500.0000, 0.0000, 0.0000);
        $order->setData('discount_amount', 0);
        $order->setData('gift_cards_amount', 1500);
        $this->addItem($order, $this->getItem(1000.0000, 1000.0000, 0, 1));
        $this->addItem($order, $this->getItem(500.0000, 500.0000, 0, 1.0000));
        $final[self::TEST_CASE_NAME_18] = $order;

        //Store Credit - частичная оплата
        $order = $this->getNewOrderInstance(1500.0000, 1000.0000, 0.0000);
        $order->setData('discount_amount', 0);
        $order->setData('customer_balance_amount', 500);
        $this->addItem($order, $this->getItem(1000.0000, 1000.0000, 0, 1));
        $this->addItem($order, $this->getItem(500.0000, 500.0000, 0, 1.0000));
        $final[self::TEST_CASE_NAME_19] = $order;

        //Стоимость доставки 315 Р надо распределить по товарам.
        $order = $this->getNewOrderInstance(14356.6000, 14671.6000, 0.0000, -315.0000);
        $this->addItem($order, $this->getItem(5600.0000, 1120.0000, 0.0000, 5.0000));
        $this->addItem($order, $this->getItem(8225.1000, 2741.7000, 0.0000, 3.0000));
        $this->addItem($order, $this->getItem(531.5000, 531.5000, 0.0000, 1.0000));
        $final[self::TEST_CASE_NAME_20] = $order;

        $order = $this->getNewOrderInstance(27420.0000, 17431.0100, 0.0000, 0);
        $this->addItem($order, $this->getItem(9990.0000, 9990.0000, 9989.0000, 1.0000));
        $this->addItem($order, $this->getItem(870.0000, 29.0000, -0.0100, 30.0000));
        $this->addItem($order, $this->getItem(1480.0000, 37.0000, 0, 40.0000));
        $this->addItem($order, $this->getItem(1480.0000, 37.0000, 0, 40.0000));
        $this->addItem($order, $this->getItem(1480.0000, 37.0000, 0, 40.0000));
        $this->addItem($order, $this->getItem(1480.0000, 37.0000, 0, 40.0000));
        $this->addItem($order, $this->getItem(1480.0000, 37.0000, 0, 40.0000));
        $this->addItem($order, $this->getItem(360.0000, 36.0000, 0, 10.0000));
        $this->addItem($order, $this->getItem(1740.0000, 29.0000, 0, 60.0000));
        $this->addItem($order, $this->getItem(2320.0000, 29.0000, 0, 80.0000));
        $this->addItem($order, $this->getItem(990.0000, 33.0000, 0, 30.0000));
        $this->addItem($order, $this->getItem(660.0000, 33.0000, 0, 20.0000));
        $this->addItem($order, $this->getItem(330.0000, 33.0000, 0, 10.0000));
        $this->addItem($order, $this->getItem(920.0000, 46.0000, 0, 20.0000));
        $this->addItem($order, $this->getItem(920.0000, 46.0000, 0, 20.0000));
        $this->addItem($order, $this->getItem(920.0000, 46.0000, 0, 20.0000));
        $this->addItem($order, $this->getItem(0.0000, 0.0000, 0, 4.0000));
        $final[self::TEST_CASE_NAME_21] = $order;

        //Discount_amount does not contain TAX
        $order = $this->getNewOrderInstance(5091.7600, 10.0000, 10.0000, 0, -4243.1300);
        $item1 = $this->getItem(3150.0000, 3150.0000, 2625.0000, 1, 20);
        $item1->setRowTotal(2625.0000);//Without Tax
        $this->addItem($order, $item1);
        $item2 = $this->getItem(1941.7600, 1941.7600, 1618.1300, 1, 20);
        $item2->setRowTotal(1618.1300);//Without Tax
        $this->addItem($order, $item2);
        $final[self::TEST_CASE_NAME_22] = $order;

        //Discount_amount contains TAX
        $order = $this->getNewOrderInstance(1941.76, 200.0000, 200.0000, 0, -1941.76);
        $item1 = $this->getItem(0, 0, 0, 1, 20);
        $item1->setRowTotal(0);//Without Tax
        $this->addItem($order, $item1);
        $item2 = $this->getItem(1941.7600, 1941.7600, 1941.76, 1, 20, 323.6300);
        $item2->setRowTotal(1618.1300);//Without Tax
        $this->addItem($order, $item2);
        $final[self::TEST_CASE_NAME_23] = $order;

        //Shipping discount amount exists.
        $order = $this->getNewOrderInstance(1200.0000, 0.0000, 75.0000, 0, -1275.0000);
        $order->setShippingDiscountAmount(75.0000);
        $order->setShippingAmount(75.0000);
        $item1 = $this
            ->getItem(1200.0000, 1200.0000, 1200.0000, 1, 20)
            ->setRowTotal(1000.0000)
            ->setTaxAmount(200.0000);
        $this->addItem($order, $item1);
        $final[self::TEST_CASE_NAME_24] = $order;

        //Была ошибка: Отрицательная сумма для кофемашины и доставки
        $order = $this->getNewOrderInstance(29820.0000, 19830.0000, 0);
        $this->addItem($order, $this->getItem(9990.0000, 9990.0000, 9990.0000, 1, 20, 0)->setData('name', 'Кофемашина Essenza Mini Piano Black'));
        $this->addItem($order, $this->getItem(990.0000, 99.0000, 0, 10, 20, 165.0000)->setData('name', 'Кофе бленд  Master Origins Aged Sumatra'));
        $this->addItem($order, $this->getItem(410.0000, 41.0000, 0, 10, 20, 68.3300)->setData('name', 'Кофе бленд Chiaro'));
        $this->addItem($order, $this->getItem(820.0000, 41.0000, 0, 20, 20, 136.6700)->setData('name', 'Кофе бленд Nicaragua'));
        $this->addItem($order, $this->getItem(1550.0000, 31.0000, 0, 50, 20, 258.3300)->setData('name', 'Кофе бленд Cosi'));
        $this->addItem($order, $this->getItem(1860.0000, 31.0000, 0, 60, 20, 310.0000)->setData('name', 'Кофе бленд Ispirazione Roma'));
        $this->addItem($order, $this->getItem(2050.0000, 41.0000, 0, 50, 20, 341.6700)->setData('name', 'Кофе бленд India'));
        $this->addItem($order, $this->getItem(1950.0000, 39.0000, 0, 50, 20, 325.0000)->setData('name', 'Кофе бленд Ispirazione Napoli'));
        $this->addItem($order, $this->getItem(2050.0000, 41.0000, 0, 50, 20, 341.6700)->setData('name', 'Кофе бленд Colombia'));
        $this->addItem($order, $this->getItem(2050.0000, 41.0000, 0, 50, 20, 341.6600)->setData('name', 'Кофе бленд Ethiopia'));
        $this->addItem($order, $this->getItem(2050.0000, 41.0000, 0, 50, 20, 341.6700)->setData('name', 'Кофе бленд Indonesia'));
        $this->addItem($order, $this->getItem(1550.0000, 31.0000, 0, 50, 20, 258.3300)->setData('name', 'Кофе бленд Ispirazione Genova Livanto'));
        $this->addItem($order, $this->getItem(2500.0000, 50.0000, 0, 50, 20, 416.6700)->setData('name', 'Кофе бленд Barista Creations Freddo Delicato For Ice'));
        $this->addItem($order, $this->getItem(0.0000, 0.0000, 0, 4, 20, 0)->setData('name', 'Пакет для использованных капсул (только для доставки по России)'));
        $final[self::TEST_CASE_NAME_25] = $order;

        return $final;
    }

    /**
     * getExpected description
     */
    protected static function getExpected()
    {
    }

    protected function onNotSuccessfulTest(\Throwable $e)
    {
        //beautify output
        echo "\033[1;31m"; // light red
        echo "\t" . $e->getMessage() . "\n";
        echo "\033[0m"; //reset color

        throw $e;
    }

    /**
     * @param float $subTotalInclTax
     * @param float $grandTotal
     * @param float $shippingInclTax
     * @param float $rewardPoints
     * @param float|null $discountAmount
     * @return \Mygento\Base\Test\OrderMock
     */
    protected function getNewOrderInstance(
        $subTotalInclTax,
        $grandTotal,
        $shippingInclTax,
        $rewardPoints = 0.00,
        $discountAmount = null
    ) {
        $order = $this->getObjectManager()->getObject(
            \Mygento\Base\Test\OrderMock::class
        );

        $order->setData('subtotal_incl_tax', $subTotalInclTax);
        $order->setData('grand_total', $grandTotal);
        $order->setData('shipping_incl_tax', $shippingInclTax);
        $order->setData(
            'discount_amount',
            $discountAmount ??
            $grandTotal + $rewardPoints - $subTotalInclTax - $shippingInclTax
        );

        return $order;
    }
}
