<?php

namespace Bbs\Model;

use Bbs\Dao\TagDao;
use Bbs\Dao\Tag;

class TagModel
{
    /** @param array<int,Tag> $tags */
    public static function toString($tags): ?string
    {
        if (count($tags) === 0)
            return null;
        $tagValues = [];
        foreach ($tags as $tag){
            $tagValues[] = $tag->tag;
        }
        return implode(' ', $tagValues);
    }

    public static function delete(int $articleId): void
    {
        $tagDao = new TagDao(getDb());
        $tagDao->delete($articleId);
    }

    /**
     * @param ?string $tags
     */
    public static function update(int $articleId, ?string $tags): void
    {
        $tagArr = $tags === null ? [] : explode(' ', $tags);
        $tagDao = new TagDao(getDb());
        $tagDao->updateTags($articleId, $tagArr);
    }
}
