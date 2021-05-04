<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\AddressBundle\Entity\AddressType;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Oro\Bundle\InfinitePayBundle\Validator\Constraints\CustomerRequireVatId;
use Oro\Bundle\InfinitePayBundle\Validator\Constraints\CustomerRequireVatIdValidator;
use Oro\Component\Testing\Unit\EntityTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CustomerRequireVatIdValidatorTest extends \PHPUnit\Framework\TestCase
{
    use EntityTrait;

    /** @var FrontendHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $frontendHelper;

    /** @var CustomerRequireVatId */
    protected $constraint;

    /** @var CustomerRequireVatIdValidator */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->constraint = new CustomerRequireVatId();
        $this->validator = new CustomerRequireVatIdValidator();
        $this->frontendHelper = $this->createMock(FrontendHelper::class);
        $this->validator->setFrontendHelper($this->frontendHelper);
    }

    public function testValidateWhenNullCustomer()
    {
        /** @var ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject $context */
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())
            ->method('addViolation');

        $this->validator->initialize($context);
        $this->validator->validate(null, $this->constraint);
    }

    public function testValidateExceptionWhenInvalidArgumentType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be instance of "Oro\Bundle\CustomerBundle\Entity\Customer", "boolean" given'
        );

        /** @var Constraint|\PHPUnit\Framework\MockObject\MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);
        $validator = new CustomerRequireVatIdValidator();
        $validator->validate(false, $constraint);
    }

    public function testValidateWhenFrontendRequest()
    {
        $this->frontendHelper->expects($this->once())
            ->method('isFrontendRequest')
            ->willReturn(true);
        /** @var ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject $context */
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())
                ->method('addViolation');

        $this->validator->initialize($context);
        $this->validator->validate(new Customer(), $this->constraint);
    }

    /**
     * @dataProvider addressesDataProvider
     * @param array $addresses
     */
    public function testValidation($vatId, $addresses, $expectedViolation)
    {
        $this->frontendHelper->expects($this->once())
            ->method('isFrontendRequest')
            ->willReturn(false);
        /** @var Customer|\PHPUnit\Framework\MockObject\MockObject $customer */
        $customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getVatId', 'getAddresses'])
            ->getMock();

        $customer->expects(self::any())
            ->method('getVatId')
            ->willReturn($vatId);
        $customer->expects(self::any())
            ->method('getAddresses')
            ->willReturn($addresses);

        /** @var ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject $context */
        $context = $this->createMock(ExecutionContextInterface::class);
        if ($expectedViolation) {
            $context
                ->expects($this->once())
                ->method('addViolation')
                ->with('oro.infinite_pay.validators.vat_id_required');
        } else {
            $context
                ->expects($this->never())
                ->method('addViolation');
        }

        $this->validator->initialize($context);
        $this->validator->validate($customer, $this->constraint);
    }

    public function addressesDataProvider()
    {
        return [
            'empty_addresses_no_vatid' =>
                [
                    'vatId' => null,
                    'addresses' => [],
                    'expectedViolation' => false
                ],
            'eu_two_shipping_addresses_no_vatid' =>
                [
                    'vatId' => null,
                    'addresses' => [
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'DE'),
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
                    ],
                    'expectedViolation' => false
                ],
            'eu_two_shipping_addresses_vatid' =>
                [
                    'vatId' => 'a vat id',
                    'addresses' => [
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'DE'),
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
                    ],
                    'expectedViolation' => false
                ],
            'non_eu_two_shipping_addresses_no_vatid' =>
                [
                    'vatId' => null,
                    'addresses' => [
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'US'),
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'AO')
                    ],
                    'expectedViolation' => false
                ],
            'non_eu_billing_addresses_no_vatid' =>
                [
                    'vatId' => null,
                    'addresses' => [
                        $this->getTypedAddressMock([AddressType::TYPE_BILLING => 'billing label'], 'US'),
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'AO')
                    ],
                    'expectedViolation' => false
                ],
            'eu_one_billing_addresses_no_vatid' =>
                [
                    'vatId' => null,
                    'addresses' => [
                        $this->getTypedAddressMock([AddressType::TYPE_BILLING => 'billing label'], 'DE'),
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
                    ],
                    'expectedViolation' => true
                ],
            'eu_one_billing_addresses_vatid' =>
                [
                    'vatId' => 'a vat id',
                    'addresses' => [
                        $this->getTypedAddressMock([AddressType::TYPE_BILLING => 'billing label'], 'DE'),
                        $this->getTypedAddressMock([AddressType::TYPE_SHIPPING => 'shipping label'], 'PT')
                    ],
                    'expectedViolation' => false
                ]

        ];
    }

    /**
     * Get address mock.
     *
     * @param array $addressTypes
     * @param string $countryIso2
     * @param bool $isEmpty
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getTypedAddressMock(array $addressTypes, $countryIso2, $isEmpty = false)
    {
        $address = $this->getMockBuilder('Oro\Bundle\CustomerBundle\Entity\AbstractDefaultTypedAddress')
            ->disableOriginalConstructor()
            ->setMethods(['getTypes', 'isEmpty', 'getCountryIso2'])
            ->getMockForAbstractClass();

        $addressTypeEntities = new ArrayCollection();
        foreach ($addressTypes as $name => $label) {
            $addressType = new AddressType($name);
            $addressType->setLabel($label);
            $addressTypeEntities->add($addressType);
        }

        $address->expects($this->any())
            ->method('getTypes')
            ->will($this->returnValue($addressTypeEntities));

        $address->expects($this->any())
            ->method('isEmpty')
            ->will($this->returnValue($isEmpty));

        $address->expects($this->any())
            ->method('getCountryIso2')
            ->will($this->returnValue($countryIso2));

        return $address;
    }
}
