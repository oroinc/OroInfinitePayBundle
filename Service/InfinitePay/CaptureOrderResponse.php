<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

/**
 * Represents a capture order response.
 */
class CaptureOrderResponse extends GenericResponse implements ResponseBodyInterface
{
    /**
     * @var ResponseCapture
     */
    protected $RESPONSE;

    public function __construct()
    {
    }

    /**
     * @return ResponseCapture
     */
    #[\Override]
    public function getResponse()
    {
        return $this->RESPONSE;
    }

    /**
     * @param ResponseCapture $RESPONSE
     *
     * @return \Oro\Bundle\InfinitePayBundle\Service\InfinitePay\CaptureOrderResponse
     */
    public function setResponse($RESPONSE)
    {
        $this->RESPONSE = $RESPONSE;

        return $this;
    }
}
