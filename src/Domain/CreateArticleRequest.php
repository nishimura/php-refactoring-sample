<?php

namespace Bbs\Domain;

interface CreateArticleRequest
{
    public function getTitle(): string;
    public function getBody(): string;
}
