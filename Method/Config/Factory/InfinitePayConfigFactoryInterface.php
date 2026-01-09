<?php

namespace Oro\Bundle\InfinitePayBundle\Method\Config\Factory;

use Oro\Bundle\InfinitePayBundle\Entity\InfinitePaySettings;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;

/**
 * Defines the contract for InfinitePay configuration factory.
 */
interface InfinitePayConfigFactoryInterface
{
    /**
     * @param InfinitePaySettings $settings
     * @return InfinitePayConfigInterface
     */
    public function createConfig(InfinitePaySettings $settings);
}
