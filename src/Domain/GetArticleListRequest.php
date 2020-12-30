<?php

namespace Bbs\Domain;

interface GetArticleListRequest
{
    public function getSearchTag(): ?string;
}
