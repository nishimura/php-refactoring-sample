<?php

namespace Bbs\Page;

use Bbs\Db\ArticleDto;

class MemoDto
{
    /** @var array<int,ArticleDto> */
    public $articles;
    /** @var ?string */
    public $message;
    /** @var FormArticleDto */
    public $form;

    /**
     * @param array<int,ArticleDto> $articles
     */
    public function __construct(
        array $articles
        , ?string $message
        , FormArticleDto $form
    ){
        $this->articles = $articles;
        $this->message = $message;
        $this->form = $form;
    }
}
