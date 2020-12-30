<?php

namespace Bbs\Application;

use Bbs\Domain\GetArticleRequest;

class GetArticleRequestImpl implements GetArticleRequest
{
    /** @var int */
    private $id;
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    public function getId(): int
    {
        return $this->id;
    }
}
