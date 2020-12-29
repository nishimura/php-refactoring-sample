<?php

namespace Bbs\Page;

use PDO;
use Bbs\Response\FoundResponse;
use Bbs\Response\Response;
use Bbs\Session\Session;
use Bbs\Model\ArticleModel;
use Bbs\Model\ValidateException;
use Bbs\Html\MemoHtml;
use Bbs\Html\MemoDto;
use Bbs\Html\ArticleDto;

class MemoPage
{
    /** @param array<string,mixed> $query */
    public static function index($query): Response
    {
        $form = ArticleModel::getForm(maybe_int($query['id']));

        return new MemoHtml(new MemoDto(
            castList(ArticleModel::getArticleList(), ArticleDto::class)
            , Session::pop('message')
            , $form
        ));
    }

    public static function create(): Response
    {
        $form = ArticleModel::getForm(null);
        try {
            ArticleModel::create($form);

        }catch (ValidateException $e){
            return new MemoHtml(new MemoDto(
                castList(ArticleModel::getArticleList(), ArticleDto::class)
                , $e->getMessage()
                , $form
            ));
        }

        return new FoundResponse('/', '登録しました。');
    }

    /** @param array<string,mixed> $query */
    public static function update($query): Response
    {
        $id = maybe_int($query['id']);
        if ($id === null)
            return new FoundResponse('/');

        $form = ArticleModel::getForm(null);
        try {
            ArticleModel::update($id, $form);

        }catch (ValidateException $e){
            return new MemoHtml(new MemoDto(
                castList(ArticleModel::getArticleList(), ArticleDto::class)
                , $e->getMessage()
                , $form
            ));
        }

        return new FoundResponse('/', '更新しました。');
    }

    /** @param array<string,mixed> $query */
    public static function delete($query): Response
    {
        $id = maybe_int($query['id']);
        if ($id === null)
            return new FoundResponse('/');

        try {
            ArticleModel::delete($id);

        }catch (ValidateException $e){
            return new FoundResponse('/');
        }

        return new FoundResponse('/', '削除しました。');
    }
}
