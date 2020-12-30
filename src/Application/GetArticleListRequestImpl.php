<?php

namespace Bbs\Application;

use Bbs\Domain\Article\GetArticleListRequest;

class GetArticleListRequestImpl implements GetArticleListRequest
{
    /** @var ?string */
    private $tag;
    public function __construct(?string $tag)
    {
        $this->tag = $tag;
    }
    public function getSearchTag(): ?string
    {
        return $this->tag;
    }
}
