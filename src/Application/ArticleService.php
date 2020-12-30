<?php

namespace Bbs\Application;

use Bbs\Domain\Article\ArticleRepository;
use Bbs\Domain\Article\ArticleModel;
use Bbs\Domain\Tag\TagRepository;
use Bbs\Domain\Tag\TagModel;

class ArticleService
{
    /** @var ArticleRepository */
    private $articleRepository;
    /** @var TagRepository */
    private $tagRepository;
    public function __construct(
        ArticleRepository $articleRepository
        , TagRepository $tagRepository
    ){
        $this->articleRepository = $articleRepository;
        $this->tagRepository = $tagRepository;
    }
    public function getArticle(int $id): ?ArticleDto
    {
        $model = new ArticleModel($this->articleRepository);
        $article = $model->getArticle(new GetArticleRequestImpl($id));
        if ($article === null)
            return null;
        return ArticleDto::fromModel($article);
    }

    public function create(string $title, string $body, ?string $tags): void
    {
        $articleModel = new ArticleModel($this->articleRepository);
        $article = $articleModel->create(new CreateArticleRequestImpl(
            $title, $body));
        $tagModel = new TagModel($this->tagRepository);
        $tagModel->update($article->article_id, new UpdateTagsRequestImpl($tags));
    }

    public function update(int $id, string $title, string $body, ?string $tags): void
    {
        $articleModel = new ArticleModel($this->articleRepository);
        $articleModel->update($id, new UpdateArticleRequestImpl(
            $id, $title, $body));

        $tagModel = new TagModel($this->tagRepository);
        $tagModel->update($id, new UpdateTagsRequestImpl($tags));
    }

    public function delete(int $id): void
    {
        $tagModel = new TagModel($this->tagRepository);
        $tagModel->delete($id);

        $articleModel = new ArticleModel($this->articleRepository);
        $articleModel->delete(new GetArticleRequestImpl($id));
    }
}
