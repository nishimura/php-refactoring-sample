<?php

namespace Bbs\Domain\Article;

interface UpdateArticleRequest extends CreateArticleRequest
{
    public function getId(): int;
}
