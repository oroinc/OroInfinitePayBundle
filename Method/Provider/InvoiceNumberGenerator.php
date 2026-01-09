<?php

namespace Oro\Bundle\InfinitePayBundle\Method\Provider;

use Oro\Bundle\OrderBundle\Entity\Order;

/**
 * Generates invoice numbers for orders.
 */
class InvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    /**
     * @param Order $order
     *
     * @return string
     */
    #[\Override]
    public function getInvoiceNumberFromOrder(Order $order)
    {
        return $order->getIdentifier();
    }
}
