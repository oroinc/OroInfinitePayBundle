<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay\Factory;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\InfinitePayClientInterface;

/**
 * Defines the contract for InfinitePay client factory.
 */
interface InfinitePayClientFactoryInterface
{
    /**
     * @param InfinitePayConfigInterface $config
     * @param array $options
     * @return InfinitePayClientInterface
     */
    public function create(InfinitePayConfigInterface $config, array $options = []);
}
