<?php

namespace Bbs\Io\Presentation\Html;

class ArticleDto
{
    public int $article_id;
    public string $title;
    public string $body;
    public string $created_at;
    public ?string $updated_at;
    /** @var array<int,string> */
    public array $tags = [];
}
