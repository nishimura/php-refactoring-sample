<?php

namespace Bbs\Db;

class ArticleDao
{
    /** @var Db */
    private $db;
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function get(int $id): ?ArticleDto
    {
        $sql = "
select article.*, group_concat(tag, ' ') as tags
from article
left outer join tag using(article_id)
where article_id = ?
    ";
        return $this->db->selectOne($sql, [$id], ArticleDto::class);
    }

    /** @return array<int,ArticleDto> */
    public function getAll(?string $tag)
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
select article.*, tags
from article
left outer join (
  select article_id, group_concat(tag, ' ') as tags
  from tag
  group by article_id
) tag using(article_id)
%s
order by created_at desc
    ", $tagWhere);
        return $this->db->getAll($sql, $params, ArticleDto::class);
    }

    private function updateTags(ArticleDto $article): void
    {
        $tags = $article->tags ? explode(' ', $article->tags) : [];
        if ($tags){
            $tagSql = 'insert into tag values(?, ?)';
            foreach ($tags as $tag){
                $this->db->execOne($tagSql, [$article->article_id, $tag]);
            }
        }
    }

    public function update(ArticleDto $articleDto): void
    {
        $sql = 'update article set title = ?, body = ?, updated_at = current_timestamp '
             . ' where article_id = ?';
        $params = [
            $articleDto->title,
            $articleDto->body,
            $articleDto->article_id,
        ];
        $this->db->execOne($sql, $params);

        $this->db->unsafeQuery('delete from tag where article_id = ?',
                               [$articleDto->article_id]);
        $this->updateTags($articleDto);
    }

    public function create(string $title, string $body, ?string $tags): void
    {
        $sql = 'insert into article(title, body) values(?,?)';
        $this->db->execOne($sql, [$title, $body]);
        $articleId = $this->db->lastInsertId();
        if ($articleId === null)
            throw new \Exception('insert id is null');

        $article = $this->get($articleId);
        if (!$article)
            throw new \Exception('insert failed');

        $article->tags = $tags;
        $this->updateTags($article);
    }

    public function delete(ArticleDto $article): void
    {
        $this->db->unsafeQuery('delete from tag where article_id = ?',
                               [$article->article_id]);
        $this->db->unsafeQuery('delete from article where article_id = ?',
                               [$article->article_id]);
    }
}
