<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\InfinitePayBundle\Action\Provider\ArticleListProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceTotalsProvider;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Entity\OrderLineItem;
use Oro\Bundle\ProductBundle\Entity\ProductName;
use Oro\Bundle\ProductBundle\Tests\Unit\Entity\Stub\Product;
use Oro\Bundle\TaxBundle\Model\Result;
use Oro\Bundle\TaxBundle\Model\ResultElement;
use Oro\Bundle\TaxBundle\Model\TaxResultElement;

class ArticleListProviderTest extends \PHPUnit\Framework\TestCase
{
    private ArticleListProvider $articleListProvider;

    #[\Override]
    protected function setUp(): void
    {
        $invoiceTotalsProvider = $this->createMock(InvoiceTotalsProvider::class);
        $invoiceTotalsProvider->expects(self::any())
            ->method('getTax')
            ->willReturn($this->getTaxArray());

        $this->articleListProvider = new ArticleListProvider($invoiceTotalsProvider);
    }

    public function testGetArticleList()
    {
        $order = $this->createMock(Order::class);
        $order->expects(self::any())
            ->method('getLineItems')
            ->willReturn(new ArrayCollection([
                $this->getLineItem('1GB82', 'Women’s Slip-On Clog', '1998.9999999999998', 2),
                $this->getLineItem('0RT28', '220 Lumen Rechargeable Headlamp', '9999.0', 1),
            ]));

        $list = $this->articleListProvider->getArticleList($order)->getARTICLE();

        $this->assertEquals(237880, $list[0]->getArticlePriceGross());
        $this->assertEquals(199900, $list[0]->getArticlePriceNet());
        $this->assertEquals(1900, $list[0]->getArticleVatPerc());

        $this->assertEquals(1189881, $list[1]->getArticlePriceGross());
        $this->assertEquals(999900, $list[1]->getArticlePriceNet());
    }

    private function getLineItem(string $sku, string $name, float $priceNet, int $quantity): OrderLineItem
    {
        $item = new OrderLineItem();

        $product = new Product();
        $product->addName((new ProductName())->setString($name));
        $product->setSku($sku);
        $item->setProduct($product);
        $item->setPrice((new Price())->setValue($priceNet));
        $item->setQuantity($quantity);

        return $item;
    }

    private function getTaxArray(): array
    {
        $taxTotal = new ResultElement();
        $taxTotal->offsetSet('excludingTax', 12.34);
        $taxShipping = new ResultElement();
        $taxShipping->offsetSet('excludingTax', 8.40);
        $taxShipping->offsetSet('includingTax', 10.0);
        $taxTaxes = new ResultElement();

        $grossPrice1 = new ResultElement();
        $grossPrice1->offsetSet('includingTax', 2378.80);
        $vatRate1 = new TaxResultElement();
        $vatRate1->offsetSet(TaxResultElement::RATE, 0.19);

        $productTax1 = new Result();
        $productTax1->offsetSet(Result::UNIT, $grossPrice1);
        $productTax1->offsetSet(Result::TAXES, [$vatRate1]);

        $grossPrice2 = new ResultElement();
        $grossPrice2->offsetSet('includingTax', 11898.81);
        $productTax2 = new Result();
        $productTax2->offsetSet(Result::UNIT, $grossPrice2);

        $taxItems = [$productTax1, $productTax2];
        $taxData = [
            'total' => $taxTotal,
            'taxes' => $taxTaxes,
            'shipping' => $taxShipping,
            'items' => $taxItems,
        ];

        return [
            'type' => 'tax',
            'label' => 'Tax',
            'amount' => '12.59',
            'currency' => 'USD',
            'visible' => true,
            'data' => $taxData,
        ];
    }
}
