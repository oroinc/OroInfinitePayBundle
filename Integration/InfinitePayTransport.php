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

    public function init(Transport $transportEntity)
    {
        $this->settings = $transportEntity->getSettingsBag();
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return InfinitePaySettingsType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEntityFQCN()
    {
        return InfinitePaySettings::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'oro.infinite_pay.settings.label';
    }
}
