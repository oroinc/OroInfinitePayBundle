<?php

namespace Oro\Bundle\InfinitePayBundle\Method\Provider;

use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Provides order data.
 */
interface OrderProviderInterface
{
    public function getDataObjectFromPaymentTransaction(PaymentTransaction $paymentTransaction);
}
