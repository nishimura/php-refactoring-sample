<?php

namespace Bbs\Domain\Tag;

interface TagRepository
{
    /** @param array<int,string> $tags */
    public function update(int $articleId, array $tags): void;
    public function delete(int $articleId): void;
}
