<?php

namespace Bbs\Io\Page;

use Bbs\Io\Presentation\Form\ArticleFormRequest;
use Bbs\Application\ArticleDto;

class ArticleFormRequestImpl implements ArticleFormRequest
{
    /** @var ArticleDto */
    private $dto;
    public function __construct(ArticleDto $dto)
    {
        $this->dto = $dto;
    }
    public function getArticleId(): int
    {
        return $this->dto->article_id;
    }
    public function getTitle(): string
    {
        return $this->dto->title;
    }
    public function getBody(): string
    {
        return $this->dto->body;
    }
    public function getTags(): ?string
    {
        return implode(' ', $this->dto->tags);
    }
}
