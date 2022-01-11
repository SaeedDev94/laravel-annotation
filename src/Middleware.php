<?php

namespace LaravelAnnotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Middleware
{
    /**
     * @param string $name
     * @param string|string[] $arguments
     * @param string|string[] $only
     * @param string|string[] $except
     */
    function __construct(public string $name, public string | array $arguments = [], public string | array $only = [], public string | array $except = [])
    {
    }

    public array $options = [];
}
