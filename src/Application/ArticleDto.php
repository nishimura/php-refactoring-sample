<?php

namespace Bbs\Application;

use Bbs\Domain;

class ArticleDto
{
    public int $article_id;
    public string $title;
    public string $body;
    public string $created_at;
    public ?string $updated_at;
    /** @var array<int,string> */
    public array $tags = [];

    private function __construct(){}

    public static function fromModel(Domain\ArticleDto $dto): self
    {
        $obj = new self();
        $obj->article_id = $dto->article_id;
        $obj->title = $dto->title;
        $obj->body = $dto->body;
        $obj->created_at = $dto->created_at;
        $obj->updated_at = $dto->updated_at;

        foreach ($dto->tags ?: [] as $tag){
            $obj->tags[] = $tag->tag;
        }
        return $obj;
    }
}
