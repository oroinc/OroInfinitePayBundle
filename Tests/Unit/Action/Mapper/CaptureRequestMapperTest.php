<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Action\Mapper\CaptureRequestMapper;
use Oro\Bundle\InfinitePayBundle\Action\Provider\ClientDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\OrderTotalProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\OrderTotalProviderInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CaptureOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ClientData;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\OrderTotal;
use Oro\Bundle\OrderBundle\Entity\Order;

class CaptureRequestMapperTest extends \PHPUnit\Framework\TestCase
{
    /** @var ClientDataProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $clientDataProvider;

    /** @var OrderTotalProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $orderTotalProvider;

    protected function setUp(): void
    {
        $this->clientDataProvider = $this->createMock(ClientDataProvider::class);
        $this->orderTotalProvider = $this->createMock(OrderTotalProvider::class);

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

    public function test()
    {
        $orderId = 'test_order';
        $order = new Order();
        $order->setIdentifier($orderId);

        $config = $this->createMock(InfinitePayConfigInterface::class);

        $captureRequestMapper = new CaptureRequestMapper($this->clientDataProvider, $this->orderTotalProvider);
        $captureOrder = $captureRequestMapper->createRequestFromOrder($order, $config);

        $this->assertInstanceOf(CaptureOrder::class, $captureOrder);
        $this->assertEquals($orderId, $captureOrder->getRequest()->getOrderData()->getOrderId());
    }
}
