<?php

namespace LaravelAnnotation;

use Illuminate\Routing\Controller;
use Illuminate\Routing\ControllerMiddlewareOptions;
use LaravelAnnotation\Attribute\Middleware;
use ReflectionAttribute;
use ReflectionClass;

class BaseController extends Controller
{
    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return [...$this->middleware, ...$this->getMiddlewaresByAttributes()];
    }

    /**
     * Get the controller middlewares by attributes
     *
     * @see Middleware
     *
     * @return array
     */
    public function getMiddlewaresByAttributes(): array
    {
        $middlewares = [];

        /** @var ReflectionAttribute[] $attributes */
        $push = function (array $attributes, ?string $method = null) use (&$middlewares) {
            foreach ($attributes as $attribute) {
                /** @var Middleware $middleware */
                $middleware = $attribute->newInstance();

                $arguments = [];
                if ($middleware->arguments) {
                    foreach ((array) $middleware->arguments as $argument) {
                        if (gettype($argument) === 'string') $arguments[] = $argument;
                        if (gettype($argument) === 'array') $arguments[] = implode('|', $argument);
                    }
                }

                $name = $middleware->name;
                if ($arguments) $name .= ':'.implode(',', $arguments);

                $middlewares[] = [
                    'middleware' => $name,
                    'options' => &$middleware->options
                ];

                $middlewareOptions = new ControllerMiddlewareOptions($middleware->options);

                if ($method) $middlewareOptions->only((array) $method);
                elseif ($middleware->only) $middlewareOptions->only((array) $middleware->only);
                elseif ($middleware->except) $middlewareOptions->except((array) $middleware->except);
            }
        };

        $class = new ReflectionClass($this);

        // Methods
        foreach ($class->getMethods() as $method) {
            $push($method->getAttributes(Middleware::class), $method->name);
        }

        // Class
        $push($class->getAttributes(Middleware::class));

        return $middlewares;
    }
}
