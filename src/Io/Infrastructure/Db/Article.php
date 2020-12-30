<?php

namespace Bbs\Io\Infrastructure\Db;

class Article
{
    public int $article_id;
    public string $title;
    public string $body;
    public string $created_at;
    public ?string $updated_at;

    private ?string $tags_json;
    /** @var array<int,Tag> */
    public array $tags = [];

    public function __construct()
    {
        if ($this->tags_json !== null){
            $objs = json_decode($this->tags_json);
            foreach ($objs as $obj){
                if ($obj->tag === null)
                    continue;
                $this->tags[] = cast($obj, Tag::class);
            }
        }
    }
}
