<?php

namespace Bbs\Io\Infrastructure\Db;

use Bbs\Io\Infrastructure\Db\Db;
use Bbs\Domain\Tag\TagRepository;

class TagDao implements TagRepository
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
    public function update(int $articleId, $tags): void
    {
        $this->delete($articleId);
        $tagSql = 'insert into tag values(?, ?)';
        foreach ($tags as $tag){
            $this->db->execOne($tagSql, [$articleId, $tag]);
        }
    }

}
