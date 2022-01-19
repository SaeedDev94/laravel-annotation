<?php

namespace LaravelAnnotation\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Middleware
{    
    public array $options = [];

    /**
     * @param string $name
     * @param string|object|string[]|object[] $arguments
     * @param string|string[] $only
     * @param string|string[] $except
     */
    public function __construct(public string $name, public string | object | array $arguments = [], public string | array $only = [], public string | array $except = [])
    {
        //
    }
}
