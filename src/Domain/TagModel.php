<?php

namespace Bbs\Domain;

class TagModel
{
    /** @var TagRepository */
    private $repository;

    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function delete(int $articleId): void
    {
        $this->repository->delete($articleId);
    }

    public function update(int $articleId, UpdateTagsRequest $request): void
    {
        $this->repository->update($articleId, $request->getTags());
    }
}
