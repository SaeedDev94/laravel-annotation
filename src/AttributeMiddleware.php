<?php

namespace LaravelAnnotation;

use Illuminate\Routing\ControllerMiddlewareOptions;
use LaravelAnnotation\Attribute\ClassMiddleware;
use LaravelAnnotation\Attribute\Middleware;
use ReflectionAttribute;
use ReflectionClass;

trait AttributeMiddleware
{
    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return array_merge($this->middleware, $this->getMiddlewaresByAttributes());
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

        $isEnum = function (mixed $obj): bool {
            if (gettype($obj) !== 'object') return false;

            $props = get_object_vars($obj);
            return
                count($props) === 2 &&
                isset($props['name']) &&
                isset($props['value']) &&
                is_string($props['name']) &&
                (is_string($props['value']) || is_numeric($props['value']));
        };

        /** @return string[] */
        $filterArguments = function (array $arguments) use ($isEnum): array {
            $items = [];

            foreach ($arguments as $argument) {
                if (is_string($argument) && $argument !== '') $items[] = $argument;
                if ($isEnum($argument) && $argument->value !== '') $items[] = (string) $argument->value;
            }

            return $items;
        };

        /** @var ReflectionAttribute[] $attributes */
        $push = function (array $attributes, ?string $method = null) use (&$middlewares, $filterArguments) {
            foreach ($attributes as $attribute) {
                /** @var Middleware $middleware */
                $middleware = $attribute->newInstance();
                $arguments = [];

                if (!is_array($middleware->arguments)) {
                    $middleware->arguments = [$middleware->arguments];
                }

                foreach ($middleware->arguments as $argument) {
                    $items = $filterArguments(is_array($argument) ? $argument : [$argument]);
                    if ($items) {
                        $arguments[] = implode('|', $items);
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

        // ClassMiddleware (Higher priority, before methods)
        $push($class->getAttributes(ClassMiddleware::class));

        // Methods
        foreach ($class->getMethods() as $method) {
            $push($method->getAttributes(Middleware::class), $method->name);
        }

        // Class
        $push($class->getAttributes(Middleware::class));

        return $middlewares;
    }
}
