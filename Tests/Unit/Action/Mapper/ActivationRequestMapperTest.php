<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Action\Mapper\ActivationRequestMapper;
use Oro\Bundle\InfinitePayBundle\Action\Provider\ClientDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceDataProviderInterface;
use Oro\Bundle\InfinitePayBundle\Action\Provider\OrderTotalProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\OrderTotalProviderInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ActivateOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ClientData;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\OrderTotal;
use Oro\Bundle\OrderBundle\Entity\Order;

class ActivationRequestMapperTest extends \PHPUnit\Framework\TestCase
{
    /** @var ClientDataProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $clientDataProvider;

    /** @var OrderTotalProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $orderTotalProvider;

    /** @var InvoiceDataProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $invoiceDataProvider;

    #[\Override]
    protected function setUp(): void
    {
        $this->clientDataProvider = $this->createMock(ClientDataProvider::class);
        $this->orderTotalProvider = $this->createMock(OrderTotalProvider::class);
        $this->invoiceDataProvider = $this->createMock(InvoiceDataProvider::class);

        $clientData = (new ClientData())->setClientRef('client_ref')->setSecurityCd('security_cd');
        $this->clientDataProvider->expects(self::any())
            ->method('getClientData')
            ->willReturn($clientData);

        $orderData = (new OrderTotal())
            ->setTrsCurrency('EUR')
            ->setTrsAmtGross(1190)
            ->setTrsAmtNet(1000)
            ->setPayType(OrderTotalProviderInterface::PAY_TYPE_INVOICE)
            ->setRabateGross(0)
            ->setRabateNet(0)
            ->setShippingPriceGross(500)
            ->setShippingPriceNet(420)
            ->setTermsAccepted('1')
            ->setTrsDt('20170101 12:00:00')
            ->setTotalGrossCalcMethod(OrderTotalProviderInterface::TOTAL_CALC_B2B_TAX_PER_ITEM);
        $this->orderTotalProvider->expects(self::any())
            ->method('getOrderTotal')
            ->willReturn($orderData);
    }

    public function testCreateRequestFromOrder()
    {
        $order = new Order();
        $order->setIdentifier('test_order');

        $config = $this->createMock(InfinitePayConfigInterface::class);

        $activateRequestMapper = new ActivationRequestMapper(
            $this->clientDataProvider,
            $this->orderTotalProvider,
            $this->invoiceDataProvider
        );
        $activateOrder = $activateRequestMapper->createRequestFromOrder($order, $config, []);

        $this->assertInstanceOf(ActivateOrder::class, $activateOrder);
        $this->assertEquals(
            OrderTotalProviderInterface::TOTAL_CALC_B2B_TAX_PER_ITEM,
            $activateOrder->getRequest()->getOrderData()->getTotalGrossCalcMethod()
        );
    }
}
