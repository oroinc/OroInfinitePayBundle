<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\DependencyInjection\Compiler;

use Oro\Bundle\InfinitePayBundle\DependencyInjection\Compiler\ActionsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActionsCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    /** @var ActionsCompilerPass */
    private $compiler;

    protected function setUp(): void
    {
        $this->compiler = new ActionsCompilerPass();
    }

    public function testProcessRegistryDoesNotExist()
    {
        $container = new ContainerBuilder();

        $this->compiler->process($container);
    }

    public function testProcessNoTaggedServicesFound()
    {
        $container = new ContainerBuilder();
        $registryDef = $container->register('oro_infinite_pay.registry.payment_actions');

        $this->compiler->process($container);

        self::assertSame([], $registryDef->getMethodCalls());
    }

    public function testProcessWithTaggedServices()
    {
        $container = new ContainerBuilder();
        $registryDef = $container->register('oro_infinite_pay.registry.payment_actions');

        $container->register('service.name.1')
            ->addTag('payment_action', ['type' => 'purchase']);
        $container->register('service.name.2')
            ->addTag('payment_action', ['type' => 'purchase']);
        $container->register('service.name.3')
            ->addTag('payment_action', ['type' => 'purchase']);
        $container->register('service.name.4')
            ->addTag('payment_action', ['type' => 'purchase']);

        $this->compiler->process($container);

        self::assertEquals(
            [
                ['addAction', ['purchase', new Reference('service.name.1')]],
                ['addAction', ['purchase', new Reference('service.name.2')]],
                ['addAction', ['purchase', new Reference('service.name.3')]],
                ['addAction', ['purchase', new Reference('service.name.4')]]
            ],
            $registryDef->getMethodCalls()
        );
    }
}
