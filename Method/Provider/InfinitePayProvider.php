<?php

namespace Oro\Bundle\InfinitePayBundle\Method\Provider;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\Provider\InfinitePayConfigProviderInterface;
use Oro\Bundle\InfinitePayBundle\Method\Factory\InfinitePayFactoryInterface;
use Oro\Bundle\PaymentBundle\Method\Provider\AbstractPaymentMethodProvider;

class InfinitePayProvider extends AbstractPaymentMethodProvider
{
    /**
     * @var InfinitePayFactoryInterface
     */
    private $factory;

    /**
     * @var InfinitePayConfigProviderInterface
     */
    private $configProvider;

    public function __construct(
        InfinitePayConfigProviderInterface $configProvider,
        InfinitePayFactoryInterface $factory
    ) {
        parent::__construct();
        $this->configProvider = $configProvider;
        $this->factory = $factory;
    }

    #[\Override]
    protected function collectMethods()
    {
        $configs = $this->configProvider->getPaymentConfigs();
        foreach ($configs as $config) {
            $this->addInfinitePayMethod($config);
        }
    }

    protected function addInfinitePayMethod(InfinitePayConfigInterface $config)
    {
        $this->addMethod(
            $config->getPaymentMethodIdentifier(),
            $this->factory->create($config)
        );
    }
}
