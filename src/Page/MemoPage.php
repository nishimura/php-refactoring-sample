<?php

namespace Bbs\Page;

use PDO;
use Bbs\Response\FoundResponse;
use Bbs\Session\Session;

class MemoPage
{
    public static function index($query)
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
        $stmt->execute($params);

        return new MemoHtml([
            'rows' => $stmt->fetchAll(),
            'message' => Session::pop('message'),
            'article' => self::getArticle($query),
        ]);
    }

    private static function getArticle($query)
    {
        $id = maybe_int($query['id']);
        $article = (object)[
            'article_id' => null,
            'title' => null,
            'body' => null,
            'created_at' => null,
            'updated_at' => null,
            'tags' => null,
        ];
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
        $stmt->execute([$id]);
        $rows = $stmt->fetchAll();

        if ($rows){
            $article = $rows[0];
        }
        return $article;
    }

    private static function updateTags(PDO $pdo, int $id)
    {
        $tags = explode(' ', $_POST['tags'] ?? []);
        if ($tags){
            $tagSql = 'insert into tag values(?, ?)';
            $stmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag){
                $stmt->execute([$id, $tag]);
            }
        }
    }

    public static function create()
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

        self::updateTags($pdo, $articleId);

        return new FoundResponse('/', '登録しました。');
    }

    public static function update($query)
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

    public static function delete($query)
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
