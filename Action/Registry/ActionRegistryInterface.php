<?php

namespace Oro\Bundle\InfinitePayBundle\Action\Registry;

use Oro\Bundle\InfinitePayBundle\Action\ActionInterface;

/**
 * Defines the contract for action registry.
 */
interface ActionRegistryInterface
{
    public function addAction($actionType, ActionInterface $actionClass);

    /**
     * @param string $actionType
     *
     * @return ActionInterface
     *
     * @throws \Exception
     */
    public function getActionByType($actionType);
}
