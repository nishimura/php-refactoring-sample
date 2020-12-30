<?php

namespace Bbs\Domain\Article;

use Bbs\Domain\ValidateException;

class ArticleModel
{
    /** @var ArticleRepository */
    private $repository;
    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }
    /** @return array<int,ArticleDto> */
    public function getArticleList(GetArticleListRequest $request)
    {
        return $this->repository->findArticleList($request->getSearchTag());
    }
    public function getArticle(GetArticleRequest $request): ?ArticleDto
    {
        return $this->repository->findById($request->getId());
    }

    public function create(CreateArticleRequest $request): ArticleDto
    {
        return $this->repository->create($request->getTitle(), $request->getBody());
    }

    public function update(int $id, UpdateArticleRequest $request): void
    {
        $article = $this->repository->findById($request->getId());
        if (!$article)
            throw new ValidateException('データがありません。');

        $article->title = $request->getTitle();
        $article->body = $request->getBody();
        $this->repository->update($article);
    }

    public function delete(GetArticleRequest $request): void
    {
        $article = $this->getArticle($request);
        if (!$article)
            throw new ValidateException('データがありません。');
        $this->repository->delete($article);
    }
}
