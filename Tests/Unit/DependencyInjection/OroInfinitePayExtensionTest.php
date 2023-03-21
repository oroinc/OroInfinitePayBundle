<?php

namespace Oro\Bundle\InfinitePayBundle\Tests\Unit\Action\DependencyInjection;

use Oro\Bundle\InfinitePayBundle\DependencyInjection\OroInfinitePayExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroInfinitePayExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $extension = new OroInfinitePayExtension();
        $extension->load([], $container);

        self::assertNotEmpty($container->getDefinitions());
    }
}
