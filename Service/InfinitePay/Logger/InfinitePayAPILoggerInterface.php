<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay\Logger;

/**
 * Defines the contract for InfinitePay API logger.
 */
interface InfinitePayAPILoggerInterface
{
    public function logApiError($request, $response);
}
