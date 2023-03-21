<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\AddressBundle\Entity\AddressType;
use Oro\Bundle\CustomerBundle\Entity\AbstractDefaultTypedAddress;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Oro\Bundle\InfinitePayBundle\Validator\Constraints\CustomerRequireVatId;
use Oro\Bundle\InfinitePayBundle\Validator\Constraints\CustomerRequireVatIdValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CustomerRequireVatIdValidatorTest extends ConstraintValidatorTestCase
{
    /** @var FrontendHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $frontendHelper;

    protected function setUp(): void
    {
        $this->frontendHelper = $this->createMock(FrontendHelper::class);
        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected function createValidator(): CustomerRequireVatIdValidator
    {
        $validator = new CustomerRequireVatIdValidator();
        $validator->setFrontendHelper($this->frontendHelper);

        return $validator;
    }

    public function testValidateWhenNullCustomer()
    {
        $constraint = new CustomerRequireVatId();
        $this->validator->validate(null, $constraint);

        $this->assertNoViolation();
    }

    public function testValidateExceptionWhenInvalidArgumentType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be instance of "Oro\Bundle\CustomerBundle\Entity\Customer", "boolean" given'
        );

        $constraint = new CustomerRequireVatId();
        $this->validator->validate(false, $constraint);
    }

    public function testValidateWhenFrontendRequest()
    {
        $this->frontendHelper->expects($this->once())
            ->method('isFrontendRequest')
            ->willReturn(true);

        $constraint = new CustomerRequireVatId();
        $this->validator->validate(new Customer(), $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider isValidDataProvider
     */
    public function testValid(?string $vatId, array $addresses)
    {
        $customer = $this->getCustomer($vatId, $addresses);

        $this->frontendHelper->expects($this->once())
            ->method('isFrontendRequest')
            ->willReturn(false);

        $constraint = new CustomerRequireVatId();
        $this->validator->validate($customer, $constraint);

        $this->assertNoViolation();
    }

    public function isValidDataProvider(): array
    {
        return [
            'empty_addresses_no_vatid'               => [
                'vatId'     => null,
                'addresses' => []
            ],
            'eu_two_shipping_addresses_no_vatid'     => [
                'vatId'     => null,
                'addresses' => [
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'DE'),
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
                ]
            ],
            'eu_two_shipping_addresses_vatid'        => [
                'vatId'     => 'a vat id',
                'addresses' => [
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'DE'),
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
                ]
            ],
            'non_eu_two_shipping_addresses_no_vatid' => [
                'vatId'     => null,
                'addresses' => [
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'US'),
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'AO')
                ]
            ],
            'non_eu_billing_addresses_no_vatid'      => [
                'vatId'     => null,
                'addresses' => [
                    $this->getTypedAddress([AddressType::TYPE_BILLING => 'billing label'], 'US'),
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'AO')
                ]
            ],
            'eu_one_billing_addresses_vatid'         => [
                'vatId'     => 'a vat id',
                'addresses' => [
                    $this->getTypedAddress([AddressType::TYPE_BILLING => 'billing label'], 'DE'),
                    $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
                ]
            ]
        ];
    }

    public function testInvalidEuOneBillingAddresses()
    {
        $vatId = null;
        $addresses = [
            $this->getTypedAddress([AddressType::TYPE_BILLING => 'billing label'], 'DE'),
            $this->getTypedAddress([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
        ];
        $customer = $this->getCustomer($vatId, $addresses);

        $this->frontendHelper->expects($this->once())
            ->method('isFrontendRequest')
            ->willReturn(false);

        $constraint = new CustomerRequireVatId();
        $this->validator->validate($customer, $constraint);

        $this->buildViolation($constraint->message)
            ->assertRaised();
    }

    private function getCustomer(?string $vatId, array $addresses): Customer
    {
        $customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAddresses'])
            ->addMethods(['getVatId'])
            ->getMock();
        $customer->expects(self::any())
            ->method('getVatId')
            ->willReturn($vatId);
        $customer->expects(self::any())
            ->method('getAddresses')
            ->willReturn($addresses);

        return $customer;
    }

    private function getTypedAddress(
        array $addressTypes,
        string $countryIso2,
        bool $isEmpty = false
    ): AbstractDefaultTypedAddress {
        $addressTypeEntities = new ArrayCollection();
        foreach ($addressTypes as $name => $label) {
            $addressType = new AddressType($name);
            $addressType->setLabel($label);
            $addressTypeEntities->add($addressType);
        }

        $address = $this->getMockBuilder(AbstractDefaultTypedAddress::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTypes', 'isEmpty', 'getCountryIso2'])
            ->getMockForAbstractClass();
        $address->expects($this->any())
            ->method('getTypes')
            ->willReturn($addressTypeEntities);
        $address->expects($this->any())
            ->method('isEmpty')
            ->willReturn($isEmpty);

        $address->expects($this->any())
            ->method('getCountryIso2')
            ->willReturn($countryIso2);

        return $address;
    }
}
