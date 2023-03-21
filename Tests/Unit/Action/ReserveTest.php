<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action;

use Oro\Bundle\InfinitePayBundle\Action\Mapper\ReservationRequestMapper;
use Oro\Bundle\InfinitePayBundle\Action\Mapper\ReservationResponseMapper;
use Oro\Bundle\InfinitePayBundle\Action\Mapper\ResponseMapperInterface;
use Oro\Bundle\InfinitePayBundle\Action\Provider\AutomationProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\InvoiceDataProviderInterface;
use Oro\Bundle\InfinitePayBundle\Action\RequestMapperInterface;
use Oro\Bundle\InfinitePayBundle\Action\Reserve;
use Oro\Bundle\InfinitePayBundle\Gateway\GatewayInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\Provider\InfinitePayConfigProviderInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ReserveOrder;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ReserveOrderResponse;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseData;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ResponseReservation;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

class ReserveTest extends \PHPUnit\Framework\TestCase
{
    /** @var RequestMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $requestMapper;

    /** @var ResponseMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $responseMapper;

    /** @var InfinitePayConfigInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $config;

    /** @var InfinitePayConfigProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configProvider;

    protected function setUp(): void
    {
        $this->config = $this->createMock(InfinitePayConfigInterface::class);
        $this->configProvider = $this->createMock(InfinitePayConfigProviderInterface::class);
        $this->requestMapper = $this->createMock(ReservationRequestMapper::class);

        $this->configProvider->expects(self::any())
            ->method('getPaymentConfig')
            ->willReturn($this->config);

        $this->requestMapper->expects(self::any())
            ->method('createRequestFromOrder')
            ->willReturn(new ReserveOrder());

        $this->responseMapper = new ReservationResponseMapper();
    }

    public function testExecuteSuccess()
    {
        $responseSuccess = $this->getResponseReservation();
        $responseSuccess->getResponse()->getResponseData()->setStatus('1');
        $gateway = $this->createMock(GatewayInterface::class);
        $gateway->expects(self::once())
            ->method('reserve')
            ->willReturn($responseSuccess);
        $actionReserve = new Reserve(
            $gateway,
            $this->configProvider
        );
        $invoiceDataProvider = $this->createMock(InvoiceDataProviderInterface::class);
        $automationProvider = new AutomationProvider($invoiceDataProvider);

        $actionReserve->setRequestMapper($this->requestMapper);
        $actionReserve->setResponseMapper($this->responseMapper);
        $actionReserve->setAutomationProvider($automationProvider);

        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setTransactionOptions(
            ['additionalData' => '{}']
        );
        $order = new Order();
        $reserveResponse = $actionReserve->execute($paymentTransaction, $order);
        $this->assertArrayNotHasKey('successUrl', $reserveResponse);
        $this->assertTrue($reserveResponse['success']);
    }

    public function testExecuteFailure()
    {
        $responseFail = $this->getResponseReservation();
        $responseFail->getResponse()->getResponseData()->setStatus('0');
        $gateway = $this->createMock(GatewayInterface::class);
        $gateway->expects(self::once())
            ->method('reserve')
            ->willReturn($responseFail);
        $actionReserve = new Reserve(
            $gateway,
            $this->configProvider
        );
        $invoiceDataProvider = $this->createMock(InvoiceDataProviderInterface::class);
        $automationProvider = new AutomationProvider($invoiceDataProvider);

        $actionReserve->setRequestMapper($this->requestMapper);
        $actionReserve->setResponseMapper($this->responseMapper);
        $actionReserve->setAutomationProvider($automationProvider);

        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setTransactionOptions(
            ['additionalData' => '{}']
        );
        $order = new Order();

        $reserveResponse = $actionReserve->execute($paymentTransaction, $order);
        $this->assertNull($reserveResponse['successUrl']);
        $this->assertFalse($reserveResponse['success']);
    }

    public function testExecuteSuccessAutoActivation()
    {
        $responseSuccess = $this->getResponseReservation();
        $responseSuccess->getResponse()->getResponseData()->setStatus('1');
        $gateway = $this->createMock(GatewayInterface::class);
        $gateway->expects(self::exactly(2))
            ->method('reserve')
            ->willReturn($responseSuccess);
        $this->config->expects(self::exactly(2))
            ->method('isAutoActivateEnabled')
            ->willReturn(true);
        $actionReserve = new Reserve(
            $gateway,
            $this->configProvider
        );
        $invoiceDataProvider = $this->createMock(InvoiceDataProvider::class);
        $automationProvider = new AutomationProvider($invoiceDataProvider);

        $actionReserve->setRequestMapper($this->requestMapper);
        $actionReserve->setResponseMapper($this->responseMapper);
        $actionReserve->setAutomationProvider($automationProvider);

        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setTransactionOptions(
            ['additionalData' => '{}']
        );
        $order = new Order();
        $reserveResponse = $actionReserve->execute($paymentTransaction, $order);
        $this->assertArrayNotHasKey('successUrl', $reserveResponse);
        $this->assertTrue($reserveResponse['success']);

        $reserveResponse = $actionReserve->execute($paymentTransaction, $order);
        $this->assertArrayNotHasKey('successKey', $reserveResponse);
        $this->assertTrue($reserveResponse['success']);
    }

    private function getResponseReservation(): ReserveOrderResponse
    {
        $reserveOrderResponse = new ReserveOrderResponse();
        $responseReservation = new ResponseReservation();
        $reserveOrderResponse->setResponse($responseReservation);
        $responseReservation->setResponseData(new ResponseData());

        return $reserveOrderResponse;
    }
}
