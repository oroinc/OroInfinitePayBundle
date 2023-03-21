<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceTotalsProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceTotalsProviderInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PricingBundle\SubtotalProcessor\TotalProcessorProvider;
use Oro\Bundle\TaxBundle\Model\Result;
use Oro\Bundle\TaxBundle\Model\ResultElement;

class InvoiceTotalsProviderTest extends \PHPUnit\Framework\TestCase
{
    private InvoiceTotalsProviderInterface $invoiceTotalsProvider;

    protected function setUp(): void
    {
        $totalsProvider = $this->createMock(TotalProcessorProvider::class);
        $totalsProvider->expects(self::any())
            ->method('getTotalWithSubtotalsAsArray')
            ->willReturn($this->getTotals());

        $this->invoiceTotalsProvider = new InvoiceTotalsProvider($totalsProvider);
    }

    public function testGetSubtotals()
    {
        $actualGrossAmount = $this->invoiceTotalsProvider->getTotalGrossAmount(new Order());
        $this->assertEquals(162.56, $actualGrossAmount);
    }

    public function testGetTaxShipping()
    {
        /** @var ResultElement $actualTaxShipping */
        $actualTaxShipping = $this->invoiceTotalsProvider->getTaxShipping(new Order());
        $this->assertEquals(8.4, $actualTaxShipping->getExcludingTax());
        $this->assertEquals(10, $actualTaxShipping->getIncludingTax());
    }

    public function testGetTaxTotals()
    {
        /** @var ResultElement $actualTaxTotals */
        $actualTaxTotals = $this->invoiceTotalsProvider->getTaxTotals(new Order());
        $this->assertEquals(12.34, $actualTaxTotals->getExcludingTax());
    }

    public function testGetDiscount()
    {
        $actualDiscount = $this->invoiceTotalsProvider->getDiscount(new Order());
        $this->assertEquals(['amount' => 0], $actualDiscount);
    }

    private function getTotals(): array
    {
        $total = [
            'type' => 'total',
            'label' => 'Total',
            'amount' => 162.56,
            'currency' => 'USD',
            'visible' => null,
            'data' => null,
        ];

        $subtotalSubtotal = [
            'type' => 'subtotal',
            'label' => 'Subtotal',
            'amount' => 139.97,
            'currency' => 'USD',
            'visible' => true,
            'data' => null,
        ];
        $shippingCost = [
            'type' => 'shipping_cost',
            'label' => 'Shipping',
            'amount' => 10.0,
            'currency' => 'USD',
            'visible' => true,
            'data' => null,
        ];
        $taxTotal = new ResultElement();
        $taxTotal->offsetSet('excludingTax', 12.34);
        $taxShipping = new ResultElement();
        $taxShipping->offsetSet('excludingTax', 8.40);
        $taxShipping->offsetSet('includingTax', 10.0);
        $taxTaxes = new ResultElement();
        $taxItems = [new Result(), new Result()];
        $taxData = [
            'total' => $taxTotal,
            'taxes' => $taxTaxes,
            'shipping' => $taxShipping,
            'items' => $taxItems,
        ];
        $tax = [
            'type' => 'tax',
            'label' => 'Tax',
            'amount' => '12.59',
            'currency' => 'USD',
            'visible' => true,
            'data' => $taxData,
        ];

        $subtotal = [$subtotalSubtotal, $shippingCost, $tax];

        return [
            'total' => $total,
            'subtotals' => $subtotal,
        ];
    }
}
