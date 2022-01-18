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
                is_scalar($props['name']) &&
                is_scalar($props['value']);
        };

        /** @returns  string[] $getItems */
        $getItems = function (array $input) use ($isEnum): array {
            $items = [];

            foreach ($input as $item) {
                if (gettype($item) === 'string') $items[] = $item;
                if ($isEnum($item)) $items[] = (string) $item->value;
            }

            return $items;
        };

        /** @var ReflectionAttribute[] $attributes */
        $push = function (array $attributes, ?string $method = null) use (&$middlewares, $getItems) {
            foreach ($attributes as $attribute) {
                /** @var Middleware $middleware */
                $middleware = $attribute->newInstance();

                $arguments = [];
                if (gettype($middleware->arguments) !== 'array') $middleware->arguments = [$middleware->arguments];
                foreach ($middleware->arguments as $argument) {
                    if (gettype($argument) === 'array' && $items = $getItems($argument)) {
                        $arguments[] = implode('|', $items);
                    }
                    elseif ($item = $getItems([$argument])[0] ?? null) $arguments[] = $item;
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
