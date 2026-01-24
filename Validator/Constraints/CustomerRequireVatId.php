<?php

namespace Oro\Bundle\InfinitePayBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating customer VAT ID requirement.
 */
class CustomerRequireVatId extends Constraint
{
    public $message = 'oro.infinite_pay.validators.vat_id_required';

    #[\Override]
    public function getTargets(): string|array
    {
        return [self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT];
    }
}
