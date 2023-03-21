<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceTotalsProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceTotalsProviderInterface;
use Oro\Bundle\InfinitePayBundle\Action\Provider\OrderTotalProvider;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\TaxBundle\Model\ResultElement;

class OrderTotalProviderTest extends \PHPUnit\Framework\TestCase
{
    private array $subtotals = ['amount' => 10.00, 'currency' => 'EUR'];
    private array $discount = ['amount' => 2.5];
    private float $totalGrossAmount = 14.87;

    /** @var InvoiceTotalsProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $invoiceTotalsProvider;

    /** @var OrderTotalProvider */
    private $orderTotalProvider;

    protected function setUp(): void
    {
        $this->invoiceTotalsProvider = $this->createMock(InvoiceTotalsProvider::class);

        $this->invoiceTotalsProvider->expects(self::any())
            ->method('getDiscount')
            ->willReturn($this->discount);
        $this->invoiceTotalsProvider->expects(self::any())
            ->method('getTotalGrossAmount')
            ->willReturn($this->totalGrossAmount);

        $taxTotals = new ResultElement();
        $taxTotals->offsetSet('excludingTax', 15.0);
        $this->invoiceTotalsProvider->expects(self::any())
            ->method('getTaxTotals')
            ->willReturn($taxTotals);

        $taxShipping = new ResultElement();
        $taxShipping->offsetSet('excludingTax', 8.43);
        $taxShipping->offsetSet('includingTax', 10);
        $this->invoiceTotalsProvider->expects(self::any())
            ->method('getTaxShipping')
            ->willReturn($taxShipping);

        $this->orderTotalProvider = new OrderTotalProvider($this->invoiceTotalsProvider);
    }

    public function testGetOrderTotal()
    {
        $order = (new Order())->setCurrency('EUR');

        $actualOrderTotals = $this->orderTotalProvider->getOrderTotal($order);

        $this->assertEquals('1487', $actualOrderTotals->getTrsAmtGross());
        $this->assertEquals('1500', $actualOrderTotals->getTrsAmtNet());
        $this->assertEquals($this->subtotals['currency'], $actualOrderTotals->getTrsCurrency());
        $this->assertEquals('1000', $actualOrderTotals->getShippingPriceGross());
        $this->assertEquals('843', $actualOrderTotals->getShippingPriceNet());
        $this->assertEquals('1', $actualOrderTotals->getPayType());
        $this->assertEquals('250', $actualOrderTotals->getRabateNet());
        $this->assertEquals('1', $actualOrderTotals->getTermsAccepted());
        $this->assertEqualsWithDelta(
            new \DateTime(),
            \DateTime::createFromFormat('Ymd His', $actualOrderTotals->getTrsDt()),
            2.0
        );
        $this->assertEquals('3', $actualOrderTotals->getTotalGrossCalcMethod());
    }
}
