<?php

namespace Bbs\Application;

use Bbs\Domain;
use Bbs\Io\Infrastructure\Db;

class ArticleService
{
    /** @var Domain\ArticleRepository */
    private $articleRepository;
    /** @var Domain\TagRepository */
    private $tagRepository;
    public function __construct(
        Domain\ArticleRepository $articleRepository
        , Domain\TagRepository $tagRepository
    ){
        $this->articleRepository = $articleRepository;
        $this->tagRepository = $tagRepository;
    }
    public function getArticle(int $id): ?ArticleDto
    {
        $model = new Domain\ArticleModel($this->articleRepository);
        $article = $model->getArticle(new GetArticleRequestImpl($id));
        if ($article === null)
            return null;
        return ArticleDto::fromModel($article);
    }

    public function create(string $title, string $body, ?string $tags): void
    {
        $articleModel = new Domain\ArticleModel($this->articleRepository);
        $article = $articleModel->create(new CreateArticleRequestImpl(
            $title, $body));
        $tagModel = new Domain\TagModel($this->tagRepository);
        $tagModel->update($article->article_id, new UpdateTagsRequestImpl($tags));
    }

    public function update(int $id, string $title, string $body, ?string $tags): void
    {
        $articleModel = new Domain\ArticleModel($this->articleRepository);
        $articleModel->update($id, new UpdateArticleRequestImpl(
            $id, $title, $body));

        $tagModel = new Domain\TagModel($this->tagRepository);
        $tagModel->update($id, new UpdateTagsRequestImpl($tags));
    }

    public function delete(int $id): void
    {
        $tagModel = new Domain\TagModel($this->tagRepository);
        $tagModel->delete($id);

        $articleModel = new Domain\ArticleModel($this->articleRepository);
        $articleModel->delete(new GetArticleRequestImpl($id));
    }
}
