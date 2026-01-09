<?php

namespace Oro\Bundle\InfinitePayBundle\Method\View\Provider;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\Provider\InfinitePayConfigProviderInterface;
use Oro\Bundle\InfinitePayBundle\Method\View\Factory\InfinitePayViewFactoryInterface;
use Oro\Bundle\PaymentBundle\Method\View\AbstractPaymentMethodViewProvider;

/**
 * Provides InfinitePay payment method view.
 */
class InfinitePayViewProvider extends AbstractPaymentMethodViewProvider
{
    /**
     * @var InfinitePayViewFactoryInterface
     */
    private $factory;

    /**
     * @var InfinitePayConfigProviderInterface
     */
    private $configProvider;

    public function __construct(
        InfinitePayViewFactoryInterface $factory,
        InfinitePayConfigProviderInterface $configProvider
    ) {
        $this->factory = $factory;
        $this->configProvider = $configProvider;
        parent::__construct();
    }

    #[\Override]
    protected function buildViews()
    {
        $configs = $this->configProvider->getPaymentConfigs();
        foreach ($configs as $config) {
            $this->addInfinitePayView($config);
        }
    }

    protected function addInfinitePayView(InfinitePayConfigInterface $config)
    {
        $this->addView(
            $config->getPaymentMethodIdentifier(),
            $this->factory->create($config)
        );
    }
}
