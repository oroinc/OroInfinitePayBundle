<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Behat\Mock\Service\InfinitePay\Factory;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\Factory\InfinitePayClientFactoryInterface;
use Oro\Bundle\InfinitePayBundle\Tests\Behat\Mock\Service\InfinitePay\InfinitePayClientMock;

class InfinitePayClientFactoryMock implements InfinitePayClientFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(InfinitePayConfigInterface $config, array $options = [])
    {
        return new InfinitePayClientMock($config, $options);
    }
}
