<?php

namespace Bbs\Domain\Tag;

interface UpdateTagsRequest
{
    /** @return array<int,string> */
    public function getTags(): array;
}
