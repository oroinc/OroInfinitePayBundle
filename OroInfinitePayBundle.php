<?php

namespace Oro\Bundle\InfinitePayBundle;

use Oro\Bundle\InfinitePayBundle\DependencyInjection\Compiler\ActionsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OroInfinitePayBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ActionsCompilerPass());
    }
}
