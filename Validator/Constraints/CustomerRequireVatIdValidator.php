<?php

namespace Oro\Bundle\InfinitePayBundle\Validator\Constraints;

use Oro\Bundle\AddressBundle\Entity\AddressType;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerAddress;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Oro\Bundle\TaxBundle\Matcher\EuropeanUnionHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CustomerRequireVatIdValidator extends ConstraintValidator
{
    /**
     * @var FrontendHelper
     */
    private $frontendHelper;

    /**
     * @param FrontendHelper $frontendHelper
     */
    public function setFrontendHelper(FrontendHelper $frontendHelper)
    {
        $this->frontendHelper = $frontendHelper;
    }

    /**
     * @param Customer $value
     * @param Constraint $constraint
     * @throws \InvalidArgumentException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Customer) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Value must be instance of "%s", "%s" given',
                    Customer::class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if ($this->frontendHelper->isFrontendRequest()) {
            return;
        }

        if ($this->getBillingEUAddesses($value) && empty($value->getVatId())) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param Customer $customer
     * @return array|CustomerAddress[]
     */
    protected function getBillingEUAddesses(Customer $customer)
    {
        $billingEUAddresses = [];

        foreach ($customer->getAddresses() as $address) {
            if ($address->isEmpty()
                || !EuropeanUnionHelper::isEuropeanUnionCountry($address->getCountryIso2())) {
                continue;
            }

            $billingType = $address->getTypes()->filter(
                function (AddressType $addressType) {
                    return $addressType->getName() === AddressType::TYPE_BILLING;
                }
            );

            if (!$billingType->isEmpty()) {
                $billingEUAddresses[] = $address;
            }
        }

        return $billingEUAddresses;
    }
}
