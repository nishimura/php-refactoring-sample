<?php

namespace Bbs\Io\Presentation\Form;

class ArticleFormService
{
    public static function getForm(?ArticleFormRequest $request): FormArticleDto
    {
        $form = new FormArticleDto();
        if ($request !== null){
            $form->article_id = $request->getArticleId();
            $form->title = $request->getTitle();
            $form->body = $request->getBody();
            $form->tags = $request->getTags();
        }

        if ($_POST){
            $form->title = maybe_string($_POST['title']);
            $form->body = maybe_string($_POST['body']);
            $form->tags = maybe_string($_POST['tags']);
        }

        return $form;
    }
}
