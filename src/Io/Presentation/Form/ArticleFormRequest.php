<?php

namespace Bbs\Io\Presentation\Form;

interface ArticleFormRequest
{
    public function getArticleId(): int;
    public function getTitle(): string;
    public function getBody(): string;
    public function getTags(): ?string;
}
