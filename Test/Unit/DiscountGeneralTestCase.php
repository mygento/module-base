<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit;

use Mygento\Base\Test\OrderMockBuilder;
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
    const TEST_CASE_NAME_25 = '#case 25. Баг с отрицательной суммой товара (макс. цена в заказе) и отрицательной суммой доставки';
    const TEST_CASE_NAME_26 = '#case 26. Баг с отрицательной стоимостью товара если есть Reward Points';
    public const TEST_CASE_NAME_27 = '#case 27. Баг с отрицательной стоимостью товара если есть Gift Card + позиция со скидкой';
    public const TEST_CASE_NAME_28 = '#case 28. Division by zero';
    public const TEST_CASE_NAME_29 = '#case 29. Bug with taxes. Скидка на доставку содержит предварительно рассчитанный налог';
    public const TEST_CASE_NAME_30 = '#case 30. Bug with taxes. Скидка на доставку не содержит предварительно посчитанного налога';

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
        $order = OrderMockBuilder::getNewOrderInstance(13380.0000, 12069.3000, 0.0000);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(12990.0000, 12990.0000, 1299.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(390.0000, 390.0000, 11.7000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000));
        $final[self::TEST_CASE_NAME_1] = $order;

        $order = OrderMockBuilder::getNewOrderInstance(5125.8600, 9373.1900, 4287.00);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(5054.4000, 5054.4000, 0.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(71.4600, 23.8200, 39.6700, 3));
        $final[self::TEST_CASE_NAME_2] = $order;

        //39.67 Discount на весь заказ. Magento размазывает по товарам скидку в заказе.
        $order = OrderMockBuilder::getNewOrderInstance(5125.8600, 5106.1900, 20.00);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(5054.4000, 5054.4000, 39.1200));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(71.4600, 23.8200, 0.5500, 3));
        $final[self::TEST_CASE_NAME_3] = $order;

        $order = OrderMockBuilder::getNewOrderInstance(5000.8600, 5200.8600, 200.00);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1000.8200, 500.4100, 0.0000, 2));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(4000.04, 1000.01, 0.0000, 4));
        $final[self::TEST_CASE_NAME_4] = $order;

        $order = OrderMockBuilder::getNewOrderInstance(222, 202.1, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(120, 40, 19, 3));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(102, 25.5, 0.9, 4));
        $final[self::TEST_CASE_NAME_5] = $order;

        $order = OrderMockBuilder::getNewOrderInstance(722, 702.1, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(120, 40, 19, 3));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(102, 25.5, 0.9, 4));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(500, 100, 0, 5));
        $final[self::TEST_CASE_NAME_6] = $order;

        //Bug GrandTotal заказа меньше, чем сумма всех позиций. На 1 товар скидка 100%
        $order = OrderMockBuilder::getNewOrderInstance(13010.0000, 11691.0000, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(12990.0000, 12990.0000, 1299.0000, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(20.0000, 20.0000, 20.0000, 1));
        $final[self::TEST_CASE_NAME_7] = $order;

        //Reward Points included
        $order = OrderMockBuilder::getNewOrderInstance(13010.0000, 11611.0000, 0, 100);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(12990.0000, 12990.0000, 1299.0000, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(20.0000, 20.0000, 0.0000, 1));
        $final[self::TEST_CASE_NAME_8] = $order;

        //Reward Points included 2
        $order = OrderMockBuilder::getNewOrderInstance(12990.0000, 12890.0000, 0, 100);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(12990.0000, 12990.0000, 0.0000, 1));
        $final[self::TEST_CASE_NAME_9] = $order;

        //Reward Points included 3
        $order = OrderMockBuilder::getNewOrderInstance(13010.0000, 12909.9900, 0, 100.01);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(12990.0000, 12990.0000, 0.0000, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(20.0000, 20.0000, 0.0000, 1));
        $final[self::TEST_CASE_NAME_10] = $order;

        //Very bad order 100374806 (prd nn)
        $order = OrderMockBuilder::getNewOrderInstance(37130.0100, 32130.0100, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(19990.0000, 19990.0000, 0.0000, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(14500.0000, 29.0000, 5000.0000, 500));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1000.0100, 1000.0000, 0.0000, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1640.0000, 410.0000, 0.0000, 4));
        $final[self::TEST_CASE_NAME_11] = $order;

        //Ошибка в расчете Magento. -1 коп уходили в доставку
        $order = OrderMockBuilder::getNewOrderInstance(18189.9900, 13189.9900, 0.0000, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(7990.0000, 7990.0000, 0, 1.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(360.0000, 36.0000, 176.4700, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1800.00, 36.00, 882.35, 50.00));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(330.0000, 33.0000, 161.7600, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(720.0000, 36.0000, 352.9400, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_12] = $order;

        //Ошибка в расчете Magento. -1 коп уходили в доставку. Без большого продукта
        $order = OrderMockBuilder::getNewOrderInstance(10199.9900, 5199.9900, 0.0000, 0.00);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(360.0000, 36.0000, 176.4700, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(330.0000, 33.0000, 161.7600, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(720.0000, 36.0000, 352.9400, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_13] = $order;

        //Такой же как и предыдущий, только копейка в +
        $order = OrderMockBuilder::getNewOrderInstance(18190.0100, 13190.0100, 0.0000, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(7990.0000, 7990.0000, 0, 1.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(360.0000, 36.0000, 176.4700, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(330.0000, 33.0000, 161.7600, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(720.0000, 36.0000, 352.9400, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_14] = $order;

        //тестируем размазывание мелких ревардов
        $order = OrderMockBuilder::getNewOrderInstance(18190.0000, 13189.6900, 0.0000, 0.31);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(7990.0000, 7990.0000, 0, 1.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(360.0000, 36.0000, 176.4700, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(330.0000, 33.0000, 161.7600, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(720.0000, 36.0000, 352.9400, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_15] = $order;

        //тестируем размазывание мелких ревардов
        $order = OrderMockBuilder::getNewOrderInstance(10200, 5190.0100, 0.0000, 9.99);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1440.0000, 36.0000, 705.8800, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1160.0000, 29.0000, 568.6300, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1450.0000, 29.0000, 710.7800, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1080.0000, 36.0000, 529.4100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(360.0000, 36.0000, 176.4700, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1800.0000, 36.0000, 882.3500, 50.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(330.0000, 33.0000, 161.7600, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(720.0000, 36.0000, 352.9400, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(780.0000, 39.0000, 382.3700, 20.0000));
        $final[self::TEST_CASE_NAME_16] = $order;

        //Случай совсем из фантастики
        $order = OrderMockBuilder::getNewOrderInstance(12989.9900, 7989.9900, 0.0000, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(5000.0000, 50.0000, 5000.0000, 100.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(7990.0000, 7990.0000, 0, 1.0000));
        $final[self::TEST_CASE_NAME_17] = $order;

        //Gift Card - полная оплата
        $order = OrderMockBuilder::getNewOrderInstance(1500.0000, 0.0000, 0.0000);
        $order->setData('discount_amount', 0);
        $order->setData('gift_cards_amount', 1500);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1000.0000, 1000.0000, 0, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(500.0000, 250.0000, 0, 2));
        $final[self::TEST_CASE_NAME_18] = $order;

        //Store Credit - частичная оплата
        $order = OrderMockBuilder::getNewOrderInstance(1500.0000, 1000.0000, 0.0000);
        $order->setData('discount_amount', 0);
        $order->setData('customer_balance_amount', 500);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1000.0000, 500.0000, 0, 2));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(500.0000, 250.0000, 0, 2.0000));
        $final[self::TEST_CASE_NAME_19] = $order;

        //Стоимость доставки 315 Р надо распределить по товарам.
        $order = OrderMockBuilder::getNewOrderInstance(14356.6000, 14671.6000, 0.0000, -315.0000);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(5600.0000, 1120.0000, 0.0000, 5.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(8225.1000, 2741.7000, 0.0000, 3.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(531.5000, 531.5000, 0.0000, 1.0000));
        $final[self::TEST_CASE_NAME_20] = $order;

        $order = OrderMockBuilder::getNewOrderInstance(27420.0000, 17431.0100, 0.0000, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(9990.0000, 9990.0000, 9989.0000, 1.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(870.0000, 29.0000, -0.0100, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1480.0000, 37.0000, 0, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1480.0000, 37.0000, 0, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1480.0000, 37.0000, 0, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1480.0000, 37.0000, 0, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1480.0000, 37.0000, 0, 40.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(360.0000, 36.0000, 0, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1740.0000, 29.0000, 0, 60.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(2320.0000, 29.0000, 0, 80.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(990.0000, 33.0000, 0, 30.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(660.0000, 33.0000, 0, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(330.0000, 33.0000, 0, 10.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(920.0000, 46.0000, 0, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(920.0000, 46.0000, 0, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(920.0000, 46.0000, 0, 20.0000));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0, 4.0000));
        $final[self::TEST_CASE_NAME_21] = $order;

        //Discount_amount does not contain TAX
        $order = OrderMockBuilder::getNewOrderInstance(5091.7600, 10.0000, 10.0000, 0, -4243.1300);
        $item1 = OrderMockBuilder::getItem(3150.0000, 3150.0000, 2625.0000, 1, 20);
        $item1->setRowTotal(2625.0000);//Without Tax
        OrderMockBuilder::addItem($order, $item1);
        $item2 = OrderMockBuilder::getItem(1941.7600, 1941.7600, 1618.1300, 1, 20);
        $item2->setRowTotal(1618.1300);//Without Tax
        OrderMockBuilder::addItem($order, $item2);
        $final[self::TEST_CASE_NAME_22] = $order;

        //Discount_amount contains TAX
        $order = OrderMockBuilder::getNewOrderInstance(1941.76, 200.0000, 200.0000, 0, -1941.76);
        $item1 = OrderMockBuilder::getItem(0, 0, 0, 1, 20);
        $item1->setRowTotal(0);//Without Tax
        OrderMockBuilder::addItem($order, $item1);
        $item2 = OrderMockBuilder::getItem(1941.7600, 1941.7600, 1941.76, 1, 20, 323.6300);
        $item2->setRowTotal(1618.1300);//Without Tax
        OrderMockBuilder::addItem($order, $item2);
        $final[self::TEST_CASE_NAME_23] = $order;

        //Shipping discount amount exists.
        $order = OrderMockBuilder::getNewOrderInstance(1200.0000, 0.0000, 75.0000, 0, -1275.0000);
        $order->setShippingDiscountAmount(75.0000);
        $order->setShippingAmount(75.0000);
        $item1 = OrderMockBuilder::getItem(1200.0000, 1200.0000, 1200.0000, 1, 20)
            ->setRowTotal(1000.0000)
            ->setTaxAmount(200.0000);
        OrderMockBuilder::addItem($order, $item1);
        $final[self::TEST_CASE_NAME_24] = $order;

        //Баг: Отрицательная стоимость для товара и доставки
        $order = OrderMockBuilder::getNewOrderInstance(29820.0000, 19830.0000, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(9990.0000, 9990.0000, 9990.0000, 1, 20, 0)->setData('name', 'Product 1'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(990.0000, 99.0000, 0, 10, 20, 165.0000)->setData('name', 'Product 2'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(410.0000, 41.0000, 0, 10, 20, 68.3300)->setData('name', 'Product 3'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(820.0000, 41.0000, 0, 20, 20, 136.6700)->setData('name', 'Product 4'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1550.0000, 31.0000, 0, 50, 20, 258.3300)->setData('name', 'Product 5'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1860.0000, 31.0000, 0, 60, 20, 310.0000)->setData('name', 'Product 6'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(2050.0000, 41.0000, 0, 50, 20, 341.6700)->setData('name', 'Product 7'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1950.0000, 39.0000, 0, 50, 20, 325.0000)->setData('name', 'Product 8'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(2050.0000, 41.0000, 0, 50, 20, 341.6700)->setData('name', 'Product 9'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(2050.0000, 41.0000, 0, 50, 20, 341.6600)->setData('name', 'Product 10'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(2050.0000, 41.0000, 0, 50, 20, 341.6700)->setData('name', 'Product 11'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1550.0000, 31.0000, 0, 50, 20, 258.3300)->setData('name', 'Product 12'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(2500.0000, 50.0000, 0, 50, 20, 416.6700)->setData('name', 'Product 13'));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0, 4, 20, 0)->setData('name', 'Product 14'));
        $final[self::TEST_CASE_NAME_25] = $order;

        //Баг: Отрицательная стоимость для товара если есть Rewards Points
        $order = OrderMockBuilder::getNewOrderInstance(21218.6400, 10307.3200, 0, 302);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(10609.3200, 10609.3200, 0.0000, 1, 20, 1768.22));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(10609.3200, 10609.3200, 10609.3200, 1, 20, 1768.22));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0));
        $final[self::TEST_CASE_NAME_26] = $order;

        //Gift Card и 1 товар со скидкой
        $order = OrderMockBuilder::getNewOrderInstance(1500.0000, 0.0000, 0.0000);
        $order->setData('discount_amount', 100);
        $order->setData('gift_cards_amount', 1400);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1000.0000, 1000.0000, 100, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(500.0000, 250.0000, 0, 2));
        $final[self::TEST_CASE_NAME_27] = $order;

        //Заказ бесплатного пробника. Клиент оплачивает только доставку
        $order = OrderMockBuilder::getNewOrderInstance(0.0000, 100.0000, 100, 0);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0));
        $final[self::TEST_CASE_NAME_28] = $order;

        //Shipping discount amount включает налог.
        $order = $this->getNewOrderInstance(1200.0000, 1320.0000, 150.0000, 0, -30.0000);
        $order->setShippingDiscountAmount(30.0000);//Уже с налогом
        $order->setShippingAmount(125.0000);
        $order->setShippingTaxAmount(25.0000);
        $item1 = $this->getItem(1200.0000, 1200.0000, 0.0000, 1, 20)
            ->setRowTotal(1000.0000)
            ->setTaxAmount(200.0000);
        $this->addItem($order, $item1);
        $final[self::TEST_CASE_NAME_29] = $order;

        //Shipping discount amount не включает налог. Налог на скидку доставки считается отдельно.
        $order = $this->getNewOrderInstance(1200.0000, 1314.0000, 150.0000, 0, -30.0000);
        $order->setShippingDiscountAmount(30.0000);//Без налога
        $order->setShippingAmount(125.0000);
        $order->setShippingTaxAmount(19.0000);
        $item1 = $this->getItem(1200.0000, 1200.0000, 0.0000, 1, 20)
            ->setRowTotal(1000.0000)
            ->setTaxAmount(200.0000);
        $this->addItem($order, $item1);
        $final[self::TEST_CASE_NAME_30] = $order;

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
}
