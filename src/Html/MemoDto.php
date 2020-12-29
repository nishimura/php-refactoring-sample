<?php

namespace Bbs\Html;

use Bbs\Form\FormArticleDto;

class MemoDto
{
    /** @var array<int,ArticleDto> */
    public array $articles;
    public ?string $message;
    public FormArticleDto $form;

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
