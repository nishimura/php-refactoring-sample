<?php

namespace Bbs\Io\Infrastructure\Db;

use Bbs\Io\Infrastructure\Db\Db;
use Bbs\Domain\Article\ArticleRepository;
use Bbs\Domain\Article\ArticleDto;

class ArticleDao implements ArticleRepository
{
    /** @var Db */
    private $db;
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?ArticleDto
    {
        $sql = "
select article.*
  , json_group_array(
    json_object('article_id', tag.article_id, 'tag', tag)) as tags_json
from article
left outer join tag using(article_id)
where article_id = ?
    ";
        $ret = $this->db->selectOne($sql, [$id], Article::class);
        if (!$ret)
            return null;
        return cast($ret, ArticleDto::class);
    }

    /** @return array<int,ArticleDto> */
    public function findArticleList(?string $tag): array
    {
        $params = [];
        if ($tag === null){
            $tagWhere = '';
        }else{
            $tagWhere = '
where exists (
  select 1
  from tag
  where article_id = article.article_id
    and tag = ?
)';
            $params = [$tag];
        }

        $sql = sprintf("
select article.*, tags_json
from article
left outer join (
  select article_id
  , json_group_array(
    json_object('article_id', article_id, 'tag', tag)) as tags_json
  from tag
  group by article_id
) tag using(article_id)
%s
order by created_at desc
    ", $tagWhere);
        return castList(
            $this->db->getAll($sql, $params, Article::class), ArticleDto::class);
    }

    public function update(ArticleDto $article): void
    {
        $sql = 'update article set title = ?, body = ?, updated_at = current_timestamp '
             . ' where article_id = ?';
        $params = [
            $article->title,
            $article->body,
            $article->article_id,
        ];
        $this->db->execOne($sql, $params);
    }

    public function create(string $title, string $body): ArticleDto
    {
        $sql = 'insert into article(title, body) values(?,?)';
        $this->db->execOne($sql, [$title, $body]);
        $articleId = $this->db->lastInsertId();
        if ($articleId === null)
            throw new \Exception('insert id is null');

        $article = $this->findById($articleId);
        if (!$article)
            throw new \Exception('insert failed');

        return cast($article, ArticleDto::class);
    }

    public function delete(ArticleDto $article): void
    {
        $this->db->unsafeQuery('delete from tag where article_id = ?',
                               [$article->article_id]);
        $this->db->unsafeQuery('delete from article where article_id = ?',
                               [$article->article_id]);
    }
}
