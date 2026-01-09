<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

/**
 * Represents a reserve order response.
 */
class ReserveOrderResponse implements ResponseBodyInterface
{
    /**
     * @var ResponseReservation
     */
    protected $RESPONSE;

    public function __construct(?ResponseReservation $responseReservation = null)
    {
        $this->RESPONSE = $responseReservation;
    }

    /**
     * @return ResponseReservation
     */
    #[\Override]
    public function getResponse()
    {
        return $this->RESPONSE;
    }

    /**
     * @param ResponseReservation $RESPONSE
     *
     * @return ReserveOrderResponse
     */
    public function setResponse(ResponseReservation $RESPONSE)
    {
        $this->RESPONSE = $RESPONSE;

        return $this;
    }
}
