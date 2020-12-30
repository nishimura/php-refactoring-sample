<?php

namespace Bbs\Domain\Article;

interface CreateArticleRequest
{
    public function getTitle(): string;
    public function getBody(): string;
}
