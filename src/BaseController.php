<?php

namespace LaravelAnnotation;

use Illuminate\Routing\Controller;
use LaravelAnnotation\AttributeMiddleware;

class BaseController extends Controller
{
    use AttributeMiddleware;
}
