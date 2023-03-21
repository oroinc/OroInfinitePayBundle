<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\InfinitePayBundle\Action\PropertyAccessor\CustomerPropertyAccessor;
use Oro\Bundle\InfinitePayBundle\Action\Provider\CompanyDataProvider;
use Oro\Bundle\OrderBundle\Entity\OrderAddress;

class CompanyDataProviderTest extends \PHPUnit\Framework\TestCase
{
    private string $vatId = 'DE129274202';
    private string $companyDataName = 'test_company';
    private string $companyDataFsName = 'test_first_name';
    private string $companyDataLsName = 'test_last_name';
    private Country $billingCountry;
    private string $billingCity = 'Mainz';
    private string $street = 'Am Rosengarten 1';
    private string $zip = '55131';
    private CompanyDataProvider $companyDataProvider;

    protected function setUp(): void
    {
        $this->billingCountry = (new Country('DE'))->setIso3Code('DEU');

        $propertyAccessor = $this->createMock(CustomerPropertyAccessor::class);
        $propertyAccessor->expects(self::any())
            ->method('extractVatId')
            ->willReturn($this->vatId);

        $this->companyDataProvider = new CompanyDataProvider($propertyAccessor);
    }

    public function testGetCompanyData()
    {
        $billingAddress = new OrderAddress();
        $billingAddress
            ->setFirstName($this->companyDataFsName)
            ->setLastName($this->companyDataLsName)
            ->setCountry($this->billingCountry)
            ->setCity($this->billingCity)
            ->setStreet($this->street)
            ->setPostalCode($this->zip)
            ->setOrganization($this->companyDataName);

        $customer = new Customer();
        $actualCompanyData = $this->companyDataProvider->getCompanyData($billingAddress, $customer);

        $this->assertEquals($this->companyDataName, $actualCompanyData->getCompanyName());
        $this->assertEquals($this->companyDataFsName, $actualCompanyData->getOwnerFsName());
        $this->assertEquals($this->companyDataLsName, $actualCompanyData->getOwnerLsName());
        $this->assertEquals('DE129274202', $actualCompanyData->getComIdVat());
    }
}
