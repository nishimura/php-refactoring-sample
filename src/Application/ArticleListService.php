<?php

namespace Bbs\Application;

use Bbs\Domain;

class ArticleListService
{
    /** @var Domain\Article\ArticleRepository */
    private $repository;

    public function __construct(Domain\Article\ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<int,ArticleDto> */
    public function getArticleList(?string $tag)
    {
        $model = new Domain\Article\ArticleModel($this->repository);
        $articles = $model->getArticleList(new GetArticleListRequestImpl($tag));
        return array_map(function(Domain\Article\ArticleDto $article): ArticleDto {
            return ArticleDto::fromModel($article);
        }, $articles);
    }
}
