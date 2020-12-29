<?php

namespace Bbs\Page;

use Bbs\Db\ArticleDto;

class FormArticleDto
{
    /** @var ?int */
    public $article_id;
    /** @var ?string */
    public $title;
    /** @var ?string */
    public $body;
    /** @var ?string */
    public $tags;

    public function __construct(?ArticleDto $dbData)
    {
        if ($dbData){
            $this->article_id = $dbData->article_id;
            $this->title = $dbData->title;
            $this->body = $dbData->body;
            $this->tags = $dbData->tags;
        }

        if ($_POST){
            $this->title = maybe_string($_POST['title']);
            $this->body = maybe_string($_POST['body']);
            $this->tags = maybe_string($_POST['tags']);
        }
    }
}
