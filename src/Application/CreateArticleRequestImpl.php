<?php

namespace Bbs\Application;

use Bbs\Domain\Article\CreateArticleRequest;

class CreateArticleRequestImpl implements CreateArticleRequest
{
    /** @var string */
    private $title;
    /** @var string */
    private $body;
    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getBody(): string
    {
        return $this->body;
    }
}
