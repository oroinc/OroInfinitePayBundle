<?php

namespace Oro\Bundle\InfinitePayBundle\Action\Provider;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ReserveOrder;
use Oro\Bundle\OrderBundle\Entity\Order;

/**
 * Provides automation data for orders.
 */
interface AutomationProviderInterface
{
    public function setAutomation(ReserveOrder $reserveOrder, Order $order, InfinitePayConfigInterface $config);
}
