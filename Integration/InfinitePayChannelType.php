<?php

namespace Oro\Bundle\InfinitePayBundle\Integration;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;
use Oro\Bundle\IntegrationBundle\Provider\IconAwareIntegrationInterface;

class InfinitePayChannelType implements ChannelInterface, IconAwareIntegrationInterface
{
    const TYPE = 'infinite_pay';

    #[\Override]
    public function getLabel()
    {
        return 'oro.infinite_pay.channel_type.infinite_pay.label';
    }

    #[\Override]
    public function getIcon()
    {
        return 'bundles/oroinfinitepay/img/infinitepay-logo.png';
    }
}
