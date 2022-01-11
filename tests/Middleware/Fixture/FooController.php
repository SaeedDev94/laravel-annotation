<?php

namespace Tests\Middleware\Fixture;

use Illuminate\Http\Request;
use LaravelAnnotation\Attribute\Middleware;
use LaravelAnnotation\BaseController;

#[Middleware('one', except: 'store')]
#[Middleware('six', only: ['store', 'update'])]
class FooController extends BaseController
{
    #[Middleware('two')]
    #[Middleware('three', ['arg1', 'arg2'])]
    public function index()
    {
    }

    #[Middleware('four')]
    #[Middleware('five', 'arg')]
    public function store(Request $request)
    {
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(Request $request, string $id)
    {
    }
}
