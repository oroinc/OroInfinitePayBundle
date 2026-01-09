<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

/**
 * Represents an apply transaction response.
 */
class ApplyTransactionResponse implements ResponseBodyInterface
{
    /**
     * @var ResponseApplyTransaction
     */
    protected $RESPONSE;

    /**
     * @return ResponseApplyTransaction
     */
    #[\Override]
    public function getResponse()
    {
        return $this->RESPONSE;
    }

    /**
     * @param ResponseApplyTransaction $RESPONSE
     *
     * @return \Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ApplyTransactionResponse
     */
    public function setResponse($RESPONSE)
    {
        $this->RESPONSE = $RESPONSE;

        return $this;
    }
}
