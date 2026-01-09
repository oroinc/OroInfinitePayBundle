<?php

namespace Oro\Bundle\InfinitePayBundle\Action;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\OrderBundle\Entity\Order;

/**
 * Defines the contract for request mappers.
 */
interface RequestMapperInterface
{
    /**
     * @param Order $order
     * @param InfinitePayConfigInterface $config
     * @param array $additionalData
     *
     * @return mixed
     */
    public function createRequestFromOrder(Order $order, InfinitePayConfigInterface $config, array $additionalData);
}
