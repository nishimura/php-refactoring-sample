<?php

namespace Bbs\Application;

use Bbs\Domain;
use Bbs\Io\Infrastructure\Db;

class ArticleListService
{
    /** @var Domain\ArticleRepository */
    private $repository;

    public function __construct(Domain\ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<int,ArticleDto> */
    public function getArticleList(?string $tag)
    {
        $model = new Domain\ArticleModel($this->repository);
        $articles = $model->getArticleList(new GetArticleListRequestImpl($tag));
        return array_map(function(Domain\ArticleDto $article): ArticleDto {
            return ArticleDto::fromModel($article);
        }, $articles);
    }
}
