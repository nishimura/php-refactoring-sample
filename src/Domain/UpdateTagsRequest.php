<?php

namespace Bbs\Domain;

interface UpdateTagsRequest
{
    /** @return array<int,string> */
    public function getTags(): array;
}
