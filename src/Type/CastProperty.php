<?php

namespace Bbs\Type;

interface CastProperty
{
    /**
     * @param mixed $value
     */
    public function castAssign(string $name, $value): void;
}
