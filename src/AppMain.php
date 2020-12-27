<?php

namespace Bbs;

use PDO;

class AppMain
{
    public static function run()
    {
        $message = '';
        $message .= self::insertOrUpdate();
        $message .= self::delete();

        $article = self::getArticle();
        $rows = self::getList();

        return [
            'message' => $message,
            'article' => $article,
            'rows' => $rows,
        ];
    }

    private static function insertOrUpdate(): ?string
    {
        $message = null;

        $title = maybe_string($_POST['title']);
        $body = maybe_string($_POST['body']);
        if ($title === null ||
            $body === null)
            return null;


        $pdo = getPdo();
        if ($articleId = maybe_int($_POST['article_id'])){
            $sql = 'update article set title = ?, body = ?, updated_at = current_timestamp where article_id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $body, $articleId]);

            $pdo->exec(sprintf('delete from tag where article_id = %d', $articleId));

            $message = '更新しました。';

        }else{
            $sql = 'insert into article(title, body) values(?,?)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $body]);
            $articleId = $pdo->lastInsertId();

            $message = '登録しました。';
        }

        $tags = explode(' ', $_POST['tags'] ?? []);
        if ($tags){
            $tagSql = 'insert into tag values(?, ?)';
            $stmt = $pdo->prepare($tagSql);
            foreach ($tags as $tag){
                $stmt->execute([$articleId, $tag]);
            }
        }

        return $message;
    }

    private static function delete(): ?string
    {
        $id = maybe_int($_POST['delete_id']);
        if ($id === null)
            return null;

        $pdo = getPdo();
        $pdo->exec(sprintf('delete from tag where article_id = %d', $id));
        $pdo->exec(sprintf('delete from article where article_id = %d', $id));
        $message = '削除しました。';

        return $message;
    }

    private static function getArticle()
    {
        $id = maybe_int($_GET['id']);
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

    private static function getList()
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
        return $stmt->fetchAll();
    }
}
