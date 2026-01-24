<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

/**
 * Represents a modify reserved order request.
 */
class ModifyReservedOrder
{
    /**
     * @var RequestModify
     */
    protected $REQUEST;

    /**
     * @param RequestModify $REQUEST
     */
    public function __construct($REQUEST = null)
    {
        $this->REQUEST = $REQUEST;
    }

    /**
     * @return RequestModify
     */
    public function getRequest()
    {
        return $this->REQUEST;
    }

    /**
     * @param RequestModify $REQUEST
     *
     * @return \Oro\Bundle\InfinitePayBundle\Service\InfinitePay\ModifyReservedOrder
     */
    public function setRequest($REQUEST)
    {
        $this->REQUEST = $REQUEST;

        return $this;
    }
}
