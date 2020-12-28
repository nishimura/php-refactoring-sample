<?php

namespace Bbs\Page;

use PDO;
use Bbs\Response\FoundResponse;
use Bbs\Response\Response;
use Bbs\Session\Session;

class MemoPage
{
    /** @param array<string,mixed> $query */
    public static function index($query): Response
    {
        $pdo = getPdo();
        $tagWhere = '';
        $params = [];
        if ($search = maybe_string($_GET['tag'])){
            $tagWhere = '
where exists (
  select 1
  from tag
  where article_id = article.article_id
    and tag = ?
)';
            $params = [$search];
        }
        $stmt = $pdo->prepare(sprintf("
select article.*, tags
from article
left outer join (
  select article_id, group_concat(tag, ' ') as tags
  from tag
  group by article_id
) tag using(article_id)
%s
order by created_at desc
    ", $tagWhere));
        $stmt->setFetchMode(PDO::FETCH_CLASS, ArticleDto::class, []);
        $stmt->execute($params);

        $articles = $stmt->fetchAll() ?: [];
        return new MemoHtml(new MemoDto(
            $articles
            , Session::pop('message')
            , self::getArticle($query)
        ));
    }

    /** @param array<string,mixed> $query */
    private static function getArticle($query): FormArticleDto
    {
        $id = maybe_int($query['id']);
        $article = new FormArticleDto();
        if ($id === null)
            return $article;

        $pdo = getPdo();
        $sql = "
select article.*, group_concat(tag, ' ') as tags
from article
left outer join tag using(article_id)
where article_id = ?
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, FormArticleDto::class, []);
        $stmt->execute([$id]);
        $rows = $stmt->fetchAll();

        if ($rows){
            $article = $rows[0];
        }
        return $article;
    }

    private static function updateTags(PDO $pdo, int $id): void
    {
        $tags = maybe_string($_POST['tags']);
        $tags = $tags ? explode(' ', $tags) : [];
        if ($tags){
            $tagSql = 'insert into tag values(?, ?)';
            $stmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag){
                $stmt->execute([$id, $tag]);
            }
        }
    }

    public static function create(): Response
    {
        $message = null;

        $title = maybe_string($_POST['title']);
        $body = maybe_string($_POST['body']);
        if ($title === null ||
            $body === null)
            return new FoundResponse('/');

        $pdo = getPdo();
        $sql = 'insert into article(title, body) values(?,?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $body]);
        $articleId = $pdo->lastInsertId();

        self::updateTags($pdo, (int)$articleId);

        return new FoundResponse('/', '登録しました。');
    }

    /** @param array<string,mixed> $query */
    public static function update($query): Response
    {
        $title = maybe_string($_POST['title']);
        $body = maybe_string($_POST['body']);
        $id = maybe_int($query['id']);
        if ($title === null ||
            $body === null ||
            $id === null)
            return new FoundResponse('/');

        $pdo = getPdo();
        $sql = 'update article set title = ?, body = ?, updated_at = current_timestamp where article_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $body, $id]);

        $pdo->exec(sprintf('delete from tag where article_id = %d', $id));

        self::updateTags($pdo, $id);

        return new FoundResponse('/', '更新しました。');
    }

    /** @param array<string,mixed> $query */
    public static function delete($query): Response
    {
        $id = maybe_int($query['id']);
        if ($id === null)
            return new FoundResponse('/');

        $pdo = getPdo();
        $pdo->exec(sprintf('delete from tag where article_id = %d', $id));
        $pdo->exec(sprintf('delete from article where article_id = %d', $id));
        return new FoundResponse('/', '削除しました。');
    }
}
