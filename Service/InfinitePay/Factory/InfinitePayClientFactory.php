<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay\Factory;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\InfinitePayClient;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\Logger\InfinitePayAPILoggerInterface;

/**
 * Creates InfinitePay client instances.
 */
class InfinitePayClientFactory implements InfinitePayClientFactoryInterface
{
    /**
     * @var InfinitePayAPILoggerInterface
     */
    protected $logger;

    public function __construct(InfinitePayAPILoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[\Override]
    public function create(InfinitePayConfigInterface $config, array $options = [])
    {
        return new InfinitePayClient($config, $this->logger, $options);
    }
}
