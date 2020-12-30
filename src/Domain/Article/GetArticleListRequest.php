<?php

namespace Bbs\Domain\Article;

interface GetArticleListRequest
{
    public function getSearchTag(): ?string;
}
