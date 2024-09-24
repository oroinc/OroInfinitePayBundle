<?php

namespace Oro\Bundle\InfinitePayBundle\Gateway;

use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay as SOAP;
use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\Factory\InfinitePayClientFactoryInterface;

/**
 * @codeCoverageIgnore
 */
class SoapGateway implements GatewayInterface
{
    /**
     * @var InfinitePayClientFactoryInterface
     */
    protected $clientFactory;

    public function __construct(InfinitePayClientFactoryInterface $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param SOAP\ReserveOrder $reservation
     *
     * @param InfinitePayConfigInterface $config
     * @return SOAP\ReserveOrderResponse
     */
    #[\Override]
    public function reserve(SOAP\ReserveOrder $reservation, InfinitePayConfigInterface $config)
    {
        $client = $this->clientFactory->create($config);
        return $client->reserveOrder($reservation);
    }

    /**
     * @param SOAP\CaptureOrder $capture
     *
     * @param InfinitePayConfigInterface $config
     * @return SOAP\CaptureOrderResponse
     */
    #[\Override]
    public function capture(SOAP\CaptureOrder $capture, InfinitePayConfigInterface $config)
    {
        $client = $this->clientFactory->create($config);
        return $client->callCaptureOrder($capture);
    }

    /**
     * @param SOAP\ActivateOrder $activateOrder
     *
     * @param InfinitePayConfigInterface $config
     * @return SOAP\ActivateOrderResponse
     */
    #[\Override]
    public function activate(SOAP\ActivateOrder $activateOrder, InfinitePayConfigInterface $config)
    {
        $client = $this->clientFactory->create($config);
        return $client->activateOrder($activateOrder);
    }

    /**
     * @param SOAP\ApplyTransaction $applyTransactionRequest
     *
     * @param InfinitePayConfigInterface $config
     * @return SOAP\ApplyTransactionResponse
     */
    #[\Override]
    public function applyTransaction(SOAP\ApplyTransaction $applyTransactionRequest, InfinitePayConfigInterface $config)
    {
        $client = $this->clientFactory->create($config);
        return $client->applyTransactionOnActivatedOrder($applyTransactionRequest);
    }
}
