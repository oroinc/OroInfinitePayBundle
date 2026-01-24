<?php

namespace Oro\Bundle\InfinitePayBundle\Action\Provider;

use Oro\Bundle\InfinitePayBundle\Service\InfinitePay\OrderArticleList;
use Oro\Bundle\OrderBundle\Entity\Order;

/**
 * Provides article list for orders.
 */
interface ArticleListProviderInterface
{
    /**
     * @param Order $order
     *
     * @return OrderArticleList
     */
    public function getArticleList(Order $order);
}
