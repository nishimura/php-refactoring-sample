<?php

namespace Bbs\Page;

use PDO;
use Bbs\Response\FoundResponse;
use Bbs\Response\Response;
use Bbs\Session\Session;
use Bbs\Db;

class MemoPage
{
    /** @return array<int,Db\ArticleDto> */
    private static function getArticleList(Db\ArticleDao $articleDao)
    {
        return $articleDao->getAll(maybe_string($_GET['tag']));
    }

    /** @param array<string,mixed> $query */
    public static function index($query): Response
    {
        $tagWhere = '';
        $params = [];
        $articleDao = new Db\ArticleDao(getDb());

        $id = maybe_int($query['id']);
        if ($id === null){
            $form = new FormArticleDto(null);
        }else{
            $article = $articleDao->get($id);
            $form = new FormArticleDto($article);
        }

        return new MemoHtml(new MemoDto(
            self::getArticleList($articleDao)
            , Session::pop('message')
            , $form
        ));
    }

    public static function create(): Response
    {
        $articleDao = new Db\ArticleDao(getDb());
        $form = new FormArticleDto(null);
        if ($form->title === null ||
            $form->body === null){
            return new MemoHtml(new MemoDto(
                self::getArticleList($articleDao)
                , '未入力項目があります。'
                , $form
            ));
        }

        $articleDao->create($form->title, $form->body, $form->tags);

        return new FoundResponse('/', '登録しました。');
    }

    /** @param array<string,mixed> $query */
    public static function update($query): Response
    {
        $id = maybe_int($query['id']);
        if ($id === null)
            return new FoundResponse('/');

        $articleDao = new Db\ArticleDao(getDb());
        $article = $articleDao->get($id);
        if (!$article)
            return new FoundResponse('/');

        $form = new FormArticleDto($article);
        if ($form->title === null ||
            $form->body === null){
            return new MemoHtml(new MemoDto(
                self::getArticleList($articleDao)
                , '未入力項目があります。'
                , $form
            ));
        }

        $article->title = $form->title;
        $article->body = $form->body;
        $article->tags = $form->tags;
        $articleDao->update($article);

        return new FoundResponse('/', '更新しました。');
    }

    /** @param array<string,mixed> $query */
    public static function delete($query): Response
    {
        $id = maybe_int($query['id']);
        if ($id === null)
            return new FoundResponse('/');

        $articleDao = new Db\ArticleDao(getDb());
        $article = $articleDao->get($id);
        if (!$article)
            return new FoundResponse('/');
        $articleDao->delete($article);
;
        return new FoundResponse('/', '削除しました。');
    }
}
