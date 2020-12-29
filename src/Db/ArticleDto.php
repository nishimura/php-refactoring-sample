<?php

namespace Bbs\Db;

class ArticleDto
{
    /** @var int */
    public $article_id;
    /** @var string */
    public $title;
    /** @var string */
    public $body;
    /** @var string */
    public $created_at;
    /** @var ?string */
    public $updated_at;
    /** @var ?string */
    public $tags;
}
