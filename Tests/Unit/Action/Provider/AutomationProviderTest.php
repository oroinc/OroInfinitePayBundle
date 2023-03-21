<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Provider;

use Oro\Bundle\InfinitePayBundle\Action\Provider\AutomationProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceDataProviderInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfig;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\OrderTotal;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\RequestReservation;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ReserveOrder;
use Oro\Bundle\OrderBundle\Entity\Order;

class AutomationProviderTest extends \PHPUnit\Framework\TestCase
{
    private InvoiceDataProviderInterface $invoiceDataProvider;

    protected function setUp(): void
    {
        $this->invoiceDataProvider = $this->createMock(InvoiceDataProvider::class);
    }

    public function testCaptureOnActivationOff()
    {
        $config = new InfinitePayConfig([
            InfinitePayConfig::AUTO_CAPTURE_KEY => true,
            InfinitePayConfig::AUTO_ACTIVATE_KEY => false
        ]);

        $automationProvider = new AutomationProvider($this->invoiceDataProvider);
        $reserveOrder = $this->getReserveOrder();
        $actualReserveOrder = $automationProvider->setAutomation($reserveOrder, new Order(), $config);
        $this->assertEquals('1', $actualReserveOrder->getRequest()->getOrderData()->getAutoCapture());
        $this->assertNull($actualReserveOrder->getRequest()->getOrderData()->getAutoActivate());
    }

    public function testCaptureOnActivationOn()
    {
        $config = new InfinitePayConfig([
            InfinitePayConfig::AUTO_CAPTURE_KEY => true,
            InfinitePayConfig::AUTO_ACTIVATE_KEY => true
        ]);

        $automationProvider = new AutomationProvider($this->invoiceDataProvider);
        $reserveOrder = $this->getReserveOrder();
        $actualReserveOrder = $automationProvider->setAutomation($reserveOrder, new Order(), $config);
        $this->assertEquals('1', $actualReserveOrder->getRequest()->getOrderData()->getAutoCapture());
        $this->assertEquals('1', $actualReserveOrder->getRequest()->getOrderData()->getAutoActivate());
    }

    public function testCaptureOffActivationOff()
    {
        $config = new InfinitePayConfig([
            InfinitePayConfig::AUTO_CAPTURE_KEY => false,
            InfinitePayConfig::AUTO_ACTIVATE_KEY => false
        ]);

        $automationProvider = new AutomationProvider($this->invoiceDataProvider);
        $reserveOrder = $this->getReserveOrder();
        $actualReserveOrder = $automationProvider->setAutomation($reserveOrder, new Order(), $config);
        $this->assertNull($actualReserveOrder->getRequest()->getOrderData()->getAutoCapture());
        $this->assertNull($actualReserveOrder->getRequest()->getOrderData()->getAutoActivate());
    }

    private function getReserveOrder(): ReserveOrder
    {
        $requestReservation = new RequestReservation();
        $requestReservation->setOrderData(new OrderTotal());

        $reserveOrder = new ReserveOrder();
        $reserveOrder->setRequest($requestReservation);

        return $reserveOrder;
    }
}
