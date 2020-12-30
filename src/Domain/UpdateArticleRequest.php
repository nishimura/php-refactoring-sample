<?php

namespace Bbs\Domain;

interface UpdateArticleRequest extends CreateArticleRequest
{
    public function getId(): int;
}
