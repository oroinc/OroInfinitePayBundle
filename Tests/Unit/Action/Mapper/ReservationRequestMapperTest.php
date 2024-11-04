<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Action\Mapper\ReservationRequestMapper;
use Oro\Bundle\InfinitePayBundle\Action\Provider\ArticleListProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\ArticleListProviderInterface;
use Oro\Bundle\InfinitePayBundle\Action\Provider\ClientDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\ClientDataProviderInterface;
use Oro\Bundle\InfinitePayBundle\Action\Provider\DebtorDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\DebtorDataProviderInterface;
use Oro\Bundle\InfinitePayBundle\Action\Provider\OrderTotalProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\OrderTotalProviderInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ClientData;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CompanyData;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\DebtorData;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\OrderTotal;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ReserveOrder;
use Oro\Bundle\OrderBundle\Entity\Order;

class ReservationRequestMapperTest extends \PHPUnit\Framework\TestCase
{
    /** @var ClientDataProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $clientDataProvider;

    /** @var DebtorDataProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $debtorDataProvider;

    /** @var OrderTotalProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $orderTotalProvider;

    /** @var ArticleListProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $articleListProvider;

    #[\Override]
    protected function setUp(): void
    {
        $this->clientDataProvider = $this->createMock(ClientDataProvider::class);
        $this->orderTotalProvider = $this->createMock(OrderTotalProvider::class);
        $this->debtorDataProvider = $this->createMock(DebtorDataProvider::class);
        $this->articleListProvider = $this->createMock(ArticleListProvider::class);

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

        $companyData = (new CompanyData())
            ->setComIdNum('test_id_num')
            ->setComIdType('freelance')
            ->setCompanyName('test_company')
            ->setOwnerFsName('test_first_name')
            ->setOwnerLsName('test_last_name');
        $debtorData = (new DebtorData())
            ->setCompanyData($companyData)
            ->setBdEmai('test_email')
            ->setDbNew('2')
            ->setNegPayHist('0')
            ->setBdSalut('n/a')
            ->setIpAdd('8.8.8.8')
            ->setIsp('test isp')
            ->setBdZip('55131')
            ->setBdCountry('DE')
            ->setBdCity('Mainz')
            ->setBdStreet('Am Rosengarten 1')
            ->setBdNameFs('Anton')
            ->setBdNameLs('Müller');
        $this->debtorDataProvider->expects(self::any())
            ->method('getDebtorData')
            ->willReturn($debtorData);
    }

    public function testCreateRequestFromOrder()
    {
        $orderId = 'order_id';
        $order = new Order();
        $order->setCurrency('EUR');
        $order->setIdentifier($orderId);

        $config = $this->createMock(InfinitePayConfigInterface::class);

        $userInputEmail = 'test@testemailde';
        $userInputLegalForm = 'eV';
        $userInput = ['email' => $userInputEmail, 'legalForm' => $userInputLegalForm];

        $reservationRequestMapper = new ReservationRequestMapper(
            $this->clientDataProvider,
            $this->debtorDataProvider,
            $this->orderTotalProvider,
            $this->articleListProvider
        );
        $actualResult = $reservationRequestMapper->createRequestFromOrder($order, $config, $userInput);

        $this->assertInstanceOf(ReserveOrder::class, $actualResult);
        $actualRequest = $actualResult->getRequest();
        $this->assertEquals($userInputEmail, $actualRequest->getDebtorData()->getBdEmail());
        $this->assertEquals($userInputLegalForm, $actualRequest->getDebtorData()->getComOrPer());
        $this->assertEquals('0', $actualRequest->getOrderData()->getAutoActivate());
        $this->assertEquals('0', $actualRequest->getOrderData()->getAutoCapture());
        $this->assertEquals($orderId, $actualRequest->getOrderData()->getOrderId());
    }
}
