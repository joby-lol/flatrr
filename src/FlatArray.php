<?php
/* Flatrr | https://github.com/jobyone/flatrr | MIT License */
namespace Flatrr;

class FlatArray implements FlatArrayInterface
{
    use FlatArrayTrait;

    public function __construct(array $data = null)
    {
        $this->merge($data);
    }
}
