<?php

namespace Bbs\Model;

use Bbs\Form\FormArticleDto;
use Bbs\Dao\ArticleDao;
use Bbs\Dao\Article;

class ArticleModel
{
    public static function getForm(?int $id): FormArticleDto
    {
        $form = new FormArticleDto();
        if ($id !== null){
            $articleDao = new ArticleDao(getDb());
            $article = $articleDao->get($id);
            if ($article){
                $form->article_id = $article->article_id;
                $form->title = $article->title;
                $form->body = $article->body;
                $form->tags = TagModel::toString($article->tags);
            }
        }

        if ($_POST){
            $form->title = maybe_string($_POST['title']);
            $form->body = maybe_string($_POST['body']);
            $form->tags = maybe_string($_POST['tags']);
        }
        return $form;
    }

    /** @return array<int,ArticleDto> */
    public static function getArticleList()
    {
        $articleDao = new ArticleDao(getDb());
        $rows = $articleDao->getAll(maybe_string($_GET['tag']));
        return castList($rows, ArticleDto::class);
    }

    public static function create(FormArticleDto $form): void
    {
        if ($form->title === null ||
            $form->body === null)
            throw new ValidateException('未入力項目があります。');

        $articleDao = new ArticleDao(getDb());
        $article = $articleDao->create($form->title, $form->body);
        TagModel::update($article->article_id, $form->tags);
    }

    public static function update(int $id, FormArticleDto $form): void
    {
        if ($form->title === null ||
            $form->body === null)
            throw new ValidateException('未入力項目があります。');

        $articleDao = new ArticleDao(getDb());
        $article = $articleDao->get($id);
        if (!$article)
            throw new ValidateException('データがありません。');

        $article->title = $form->title;
        $article->body = $form->body;
        $articleDao->update($article);
        TagModel::update($article->article_id, $form->tags);
    }

    public static function delete(int $id): void
    {
        $articleDao = new ArticleDao(getDb());
        $article = $articleDao->get($id);
        if (!$article)
            throw new ValidateException('データがありません。');
        $articleDao->delete($article);
        TagModel::delete($article->article_id);
    }
}
