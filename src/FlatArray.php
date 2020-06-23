<?php
/* Flatrr | https://gitlab.com/byjoby/flatrr | MIT License */
namespace Flatrr;

class FlatArray implements FlatArrayInterface
{
    use FlatArrayTrait;

    public function __construct(array $data = null)
    {
        $this->merge($data);
    }
}
