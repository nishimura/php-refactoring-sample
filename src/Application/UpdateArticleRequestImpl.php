<?php

namespace Bbs\Application;

use Bbs\Domain\UpdateArticleRequest;

class UpdateArticleRequestImpl extends CreateArticleRequestImpl
    implements UpdateArticleRequest
{
    /** @var int */
    private $id;
    public function __construct(int $id, string $title, string $body)
    {
        $this->id = $id;
        parent::__construct($title, $body);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
