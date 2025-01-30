<?php

namespace Oro\Bundle\InfinitePayBundle\Service\InfinitePay;

class OrderArticleList
{
    /**
     * @var OrderArticle[]
     */
    protected $ARTICLE;

    public function __construct()
    {
    }

    /**
     * @return OrderArticle[]
     */
    public function getARTICLE()
    {
        return $this->ARTICLE;
    }

    /**
     * @param OrderArticle[]|null $ARTICLE
     *
     * @return OrderArticleList
     */
    public function setARTICLE(?array $ARTICLE = null)
    {
        $this->ARTICLE = $ARTICLE;

        return $this;
    }
}
