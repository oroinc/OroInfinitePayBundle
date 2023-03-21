<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\InfinitePayBundle\Action\Provider\CompanyDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\DebtorDataProvider;
use Oro\Bundle\InfinitePayBundle\Action\Provider\DebtorDataProviderInterface;
use Oro\Bundle\InfinitePayBundle\Action\Provider\RequestProvider;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CompanyData;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Entity\OrderAddress;
use Oro\Bundle\PaymentBundle\Provider\AddressExtractor;
use Oro\Component\PhpUtils\Network\DnsResolver;

class DebtorDataProviderTest extends \PHPUnit\Framework\TestCase
{
    private DebtorDataProviderInterface $debtorDataProvider;
    private string $companyDataIdNum = 'test_id_num';
    private string $companyDataIdType = 'freelance';
    private string $companyDataName = 'test_company';
    private string $companyDataFsName = 'test_first_name';
    private string $companyDataLsName = 'test_last_name';
    private string $clientIp = '8.8.8.8';
    private string $isp = 'google-public-dns-a.google.com';
    private Country $billingCountry;
    private string $billingCity = 'Mainz';
    private string $street = 'Am Rosengarten 1';
    private string $zip = '55131';

    protected function setUp(): void
    {
        $this->billingCountry = (new Country('DE'))->setIso3Code('DEU');

        $companyDataProvider = $this->createMock(CompanyDataProvider::class);

        $companyDataProvider->expects(self::any())
            ->method('getCompanyData')
            ->willReturn($this->getCompanyData());

        $requestProvider = $this->createMock(RequestProvider::class);

        $requestProvider->expects(self::any())
            ->method('getClientIp')
            ->willReturn($this->clientIp);

        $addressExtractor = $this->createMock(AddressExtractor::class);

        $addressExtractor->expects(self::any())
            ->method('extractAddress')
            ->willReturn($this->getOrderAddress());

        $dnsResolver = $this->createMock(DnsResolver::class);
        $dnsResolver->expects(self::any())
            ->method('getHostnameByIp')
            ->willReturn($this->isp);

        $this->debtorDataProvider = new DebtorDataProvider(
            $companyDataProvider,
            $requestProvider,
            $addressExtractor,
            $dnsResolver
        );
    }

    public function testGetDebtorData()
    {
        $order = new Order();
        $order->setCustomer(new Customer());
        $debtorDataActual = $this->debtorDataProvider->getDebtorData($order);

        $this->assertEquals($this->zip, $debtorDataActual->getBdZip());
        $this->assertEquals($this->clientIp, $debtorDataActual->getIpAdd());
        $this->assertEquals($this->billingCity, $debtorDataActual->getBdCity());
        $this->assertEquals($this->street, $debtorDataActual->getBdStreet());
        $this->assertEquals($this->clientIp, $debtorDataActual->getIpAdd());
        $this->assertEquals($this->isp, $debtorDataActual->getIsp());
        $this->assertEquals($this->companyDataFsName, $debtorDataActual->getBdNameFs());
        $this->assertEquals($this->companyDataLsName, $debtorDataActual->getBdNameLs());
    }

    private function getCompanyData(): CompanyData
    {
        return (new CompanyData())
            ->setComIdNum($this->companyDataIdNum)
            ->setComIdType($this->companyDataIdType)
            ->setCompanyName($this->companyDataName)
            ->setOwnerFsName($this->companyDataFsName)
            ->setOwnerLsName($this->companyDataLsName);
    }

    private function getOrderAddress(): OrderAddress
    {
        return (new OrderAddress())
            ->setFirstName($this->companyDataFsName)
            ->setLastName($this->companyDataLsName)
            ->setCountry($this->billingCountry)
            ->setCity($this->billingCity)
            ->setStreet($this->street)
            ->setPostalCode($this->zip);
    }
}
