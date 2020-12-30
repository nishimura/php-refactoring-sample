<?php

namespace Bbs\Application;

use Bbs\Domain\UpdateTagsRequest;

class UpdateTagsRequestImpl implements UpdateTagsRequest
{
    /** @var array<int,string> */
    private $tags;

    public function __construct(?string $tags)
    {
        if ($tags === null){
            $this->tags = [];
        }else{
            $this->tags = explode(' ', $tags);
        }
    }

    /** @return array<int,string> */
    public function getTags(): array
    {
        return $this->tags;
    }
}
