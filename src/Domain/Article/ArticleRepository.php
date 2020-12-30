<?php

namespace Bbs\Domain\Article;

interface ArticleRepository
{
    public function findById(int $id): ?ArticleDto;

    /** @return array<int,ArticleDto> */
    public function findArticleList(?string $tag): array;

    public function create(string $title, string $body): ArticleDto;
    public function update(ArticleDto $article): void;
    public function delete(ArticleDto $article): void;
}
