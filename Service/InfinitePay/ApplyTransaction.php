<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

/**
 * Represents an apply transaction request.
 */
class ApplyTransaction
{
    /**
     * @var RequestApplyTransaction
     */
    protected $REQUEST;

    /**
     * @param RequestApplyTransaction $REQUEST
     */
    public function __construct($REQUEST = null)
    {
        $this->REQUEST = $REQUEST;
    }

    /**
     * @return RequestApplyTransaction
     */
    public function getRequest()
    {
        return $this->REQUEST;
    }

    /**
     * @param RequestApplyTransaction $REQUEST
     *
     * @return \Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ApplyTransaction
     */
    public function setRequest($REQUEST)
    {
        $this->REQUEST = $REQUEST;

        return $this;
    }
}
