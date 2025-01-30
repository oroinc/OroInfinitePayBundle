<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

class ErrorDataList
{
    /**
     * @var ErrorData[]
     */
    protected $ERROR;

    public function __construct()
    {
    }

    /**
     * @return ErrorData[]
     */
    public function getError()
    {
        return $this->ERROR;
    }

    /**
     * @param ErrorData[]|null $ERROR
     *
     * @return ErrorDataList
     */
    public function setError(?array $ERROR = null)
    {
        $this->ERROR = $ERROR;

        return $this;
    }
}
