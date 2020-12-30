<?php

namespace Bbs\Domain;

interface TagRepository
{
    /** @param array<int,string> $tags */
    public function update(int $articleId, array $tags): void;
    public function delete(int $articleId): void;
}
