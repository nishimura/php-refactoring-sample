<?php

namespace Bbs\Dao;

use Bbs\Db\Db;

class TagDao
{
    /** @var Db */
    private $db;
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function delete(int $articleId): void
    {
        $this->db->unsafeQuery('delete from tag where article_id = ?',
                               [$articleId]);
    }

    /**
     * @param array<int,string> $tags
     */
    public function updateTags(int $articleId, $tags): void
    {
        $this->delete($articleId);
        $tagSql = 'insert into tag values(?, ?)';
        foreach ($tags as $tag){
            $this->db->execOne($tagSql, [$articleId, $tag]);
        }
    }

}
