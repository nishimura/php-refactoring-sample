<?php

namespace Bbs\Io\Page;

use PDO;
use Bbs\Io\Infrastructure\Response\FoundResponse;
use Bbs\Io\Infrastructure\Response\Response;
use Bbs\Io\Infrastructure\Session\Session;
use Bbs\Io\Presentation\Html;
use Bbs\Io\Infrastructure\Db;
use Bbs\Domain;
use Bbs\Io\Presentation\Form;
use Bbs\Application;

class MemoPage
{
    private static function getService(): Application\ArticleService
    {
        return new Application\ArticleService(new Db\ArticleDao(getDb()), new Db\TagDao(getDb()));
    }
    private static function getForm(?int $id): Form\FormArticleDto
    {
        $request = null;
        if ($id !== null){
            $service = self::getService();
            $article = $service->getArticle($id);
            if ($article)
                $request = new ArticleFormRequestImpl($article);
        }
        return Form\ArticleFormService::getForm($request);
    }

    private static function responseIndex(
        Form\FormArticleDto $form
        , ?string $message
    ): Response
    {
        $listService = new Application\ArticleListService(new Db\ArticleDao(getDb()));
        
        $articles = $listService->getArticleList(maybe_string($_GET['tag']));
        return new Html\MemoHtml(new Html\MemoDto(
            castList($articles, Html\ArticleDto::class)
            , $message
            , $form
        ));
    }

    /** @param array<string,mixed> $query */
    public static function index($query): Response
    {
        $form = self::getForm(maybe_int($query['id']));
        return self::responseIndex($form, Session::pop('message'));
    }

    public static function create(): Response
    {
        $form = self::getForm(null);

        if ($form->title === null ||
            $form->body === null)
            return self::responseIndex($form, '未入力項目があります。');

        $service = self::getService();
        try {
            $service->create($form->title, $form->body, $form->tags);

        }catch (Domain\ValidateException $e){
            return self::responseIndex($form, $e->getMessage());
        }

        return new FoundResponse('/', '登録しました。');
    }

    /** @param array<string,mixed> $query */
    public static function update($query): Response
    {
        $id = maybe_int($query['id']);
        if ($id === null)
            return new FoundResponse('/');

        $form = self::getForm($id);

        if ($form->title === null ||
            $form->body === null)
            return self::responseIndex($form, '未入力項目があります。');

        $service = self::getService();
        try {
            $service->update($id, $form->title, $form->body, $form->tags);

        }catch (Domain\ValidateException $e){
            return self::responseIndex($form, $e->getMessage());
        }

        return new FoundResponse('/', '更新しました。');
    }

    /** @param array<string,mixed> $query */
    public static function delete($query): Response
    {
        $id = maybe_int($query['id']);
        if ($id === null)
            return new FoundResponse('/');

        $service = self::getService();
        try {
            $service->delete($id);

        }catch (Domain\ValidateException $e){
            return new FoundResponse('/');
        }

        return new FoundResponse('/', '削除しました。');
    }
}
