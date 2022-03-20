<?php

namespace LaravelAnnotation;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use AttributeMiddleware;
}
