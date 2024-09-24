<?php

namespace Oro\Bundle\InfinitePayBundle\Validator\Constraints;

use Oro\Bundle\AddressBundle\Entity\AddressType;
use Oro\Bundle\CustomerBundle\Entity\AbstractDefaultTypedAddress;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;
use Oro\Bundle\TaxBundle\Matcher\EuropeanUnionHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that VAT is mandatory if a customer has at least one billing address in European Union.
 */
class CustomerRequireVatIdValidator extends ConstraintValidator
{
    /** @var FrontendHelper */
    private $frontendHelper;

    public function setFrontendHelper(FrontendHelper $frontendHelper)
    {
        $this->frontendHelper = $frontendHelper;
    }

    /**
     * @param Customer   $value
     * @param Constraint $constraint
     */
    #[\Override]
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof Customer) {
            throw new \InvalidArgumentException(sprintf(
                'Value must be instance of "%s", "%s" given',
                Customer::class,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        if ($this->frontendHelper->isFrontendRequest()) {
            return;
        }

        if (empty($value->getVatId()) && $this->hasEuropeanUnionBillingAddresses($value)) {
            $this->context->addViolation($constraint->message);
        }
    }

    private function hasEuropeanUnionBillingAddresses(Customer $customer): bool
    {
        $result = false;
        /** @var AbstractDefaultTypedAddress $address */
        foreach ($customer->getAddresses() as $address) {
            if (!$address->isEmpty()
                && EuropeanUnionHelper::isEuropeanUnionCountry($address->getCountryIso2())
                && $address->hasTypeWithName(AddressType::TYPE_BILLING)
            ) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
