<?php

/* Flatrr | https://github.com/jobyone/flatrr | MIT License */

namespace Flatrr;

class FlatArray implements FlatArrayInterface
{
    use FlatArrayTrait;

    /**
     * @param null|array<string|mixed> $data
     * @return void
     */
    public function __construct(null|array $data = null)
    {
        $this->merge($data);
    }
}
