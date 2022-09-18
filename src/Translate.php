<?php

namespace MouYong\Translate;

// use ZhenMu\Support\Traits\Arrayable;

class Translate
{
    // use Arrayable;
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getSrc()
    {
        return $this->attributes['src'];
    }

    public function getDst()
    {
        return $this->attributes['dst'];
    }

    public function getOriginal()
    {
        return $this->attributes;
    }

    public function __toString()
    {
        return $this->getDst();
    }

    public function toString(){
        return $this->__toString();
    }
}
