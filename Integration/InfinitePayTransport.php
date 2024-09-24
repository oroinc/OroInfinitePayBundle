<?php

namespace Oro\Bundle\InfinitePayBundle\Integration;

use Oro\Bundle\InfinitePayBundle\Entity\InfinitePaySettings;
use Oro\Bundle\InfinitePayBundle\Form\Type\InfinitePaySettingsType;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class InfinitePayTransport implements TransportInterface
{
    /** @var ParameterBag */
    protected $settings;

    #[\Override]
    public function init(Transport $transportEntity)
    {
        $this->settings = $transportEntity->getSettingsBag();
    }

    #[\Override]
    public function getSettingsFormType()
    {
        return InfinitePaySettingsType::class;
    }

    #[\Override]
    public function getSettingsEntityFQCN()
    {
        return InfinitePaySettings::class;
    }

    #[\Override]
    public function getLabel()
    {
        return 'oro.infinite_pay.settings.label';
    }
}
