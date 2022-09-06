<?php

namespace LaravelAnnotation\Attribute;

use Attribute;

/**
 * Basically #[Middleware] register middlewares for methods first
 * If you want to register middlewares for class before methods:
 * Use #[ClassMiddleware]
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ClassMiddleware extends Middleware
{
}
