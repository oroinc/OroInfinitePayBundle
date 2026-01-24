<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

/**
 * Defines the contract for response body.
 */
interface ResponseBodyInterface
{
    /**
     * @return GenericResponseInterface
     */
    public function getResponse();
}
