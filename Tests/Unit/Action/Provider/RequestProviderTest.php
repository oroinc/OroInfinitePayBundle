<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\Mapper;

use Oro\Bundle\InfinitePayBundle\Action\Provider\RequestProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestProviderTest extends \PHPUnit\Framework\TestCase
{
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $request = new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1'], []);

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);
    }

    public function testGetClientIp()
    {
        $requestProvider = new RequestProvider($this->requestStack);
        $this->assertEquals('127.0.0.1', $requestProvider->getClientIp());
    }
}
