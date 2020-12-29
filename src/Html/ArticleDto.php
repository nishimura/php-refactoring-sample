<?php

namespace Bbs\Html;

use Bbs\Type\CastProperty;

class ArticleDto implements CastProperty
{
    public int $article_id;
    public string $title;
    public string $body;
    public string $created_at;
    public ?string $updated_at;
    /** @var array<int,TagDto> */
    public array $tags = [];

    public function castAssign(string $name, $value): void
    {
        if ($name !== 'tags'){
            $this->$name = $value;
            return;
        }

        $this->tags = castList($value, TagDto::class);
    }
}
