<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay\Logger;

interface InfinitePayAPILoggerInterface
{
    public function logApiError($request, $response);
}
