<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Method;

use Oro\Bundle\InfinitePayBundle\Action\ActionInterface;
use Oro\Bundle\InfinitePayBundle\Action\Registry\ActionRegistryInterface;
use Oro\Bundle\InfinitePayBundle\Method\Config\InfinitePayConfigInterface;
use Oro\Bundle\InfinitePayBundle\Method\InfinitePay;
use Oro\Bundle\InfinitePayBundle\Method\Provider\OrderProviderInterface;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

class InfinitePayTest extends \PHPUnit\Framework\TestCase
{
    /** @var InfinitePayConfigInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $config;

    /** @var ActionRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $actionRegistry;

    /** @var OrderProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $orderProvider;

    /** @var InfinitePay */
    private $infinitePay;

    #[\Override]
    protected function setUp(): void
    {
        $this->config = $this->createMock(InfinitePayConfigInterface::class);
        $this->actionRegistry = $this->createMock(ActionRegistryInterface::class);
        $this->orderProvider = $this->createMock(OrderProviderInterface::class);

        $this->infinitePay = new InfinitePay($this->config, $this->actionRegistry, $this->orderProvider);
    }

    public function testSupports()
    {
        $this->assertTrue($this->infinitePay->supports('purchase'));
        $this->assertFalse($this->infinitePay->supports('unknown_method'));
    }

    public function testExecute()
    {
        $action = $this->createMock(ActionInterface::class);
        $action->expects(self::once())
            ->method('execute')
            ->willReturn(['action return value']);
        $this->actionRegistry->expects(self::once())
            ->method('getActionByType')
            ->with('purchase')
            ->willReturn($action);

        $this->orderProvider->expects(self::once())
            ->method('getDataObjectFromPaymentTransaction')
            ->willReturn(new Order());

        $this->assertEquals(
            ['action return value'],
            $this->infinitePay->execute('purchase', new PaymentTransaction())
        );
    }
}
