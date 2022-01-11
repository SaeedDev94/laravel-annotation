# LaravelAnnotation

### Introduction

PHP 8.0 release was a revolution for the language.  
It brings cool features like [`Named arguments`](https://www.php.net/releases/8.0/en.php#named-arguments), [`Attributes`](https://www.php.net/releases/8.0/en.php#attributes), [`Constructor property`](https://www.php.net/releases/8.0/en.php#constructor-property-promotion) and ...  
PHP 8.1 brings event more exciting features like [`Enumerations`](https://www.php.net/releases/8.1/en.php#enumerations), [`New in initializers`](https://www.php.net/releases/8.1/en.php#new_in_initializers), [`Array unpacking`](https://www.php.net/releases/8.1/en.php#array_unpacking_support_for_string_keyed_arrays) and ...  
**The idea of this package is using PHP [`Attribute`](https://www.php.net/manual/en/language.attributes.overview.php) in a laravel project.**

### Installing

```bash
$ composer require saeedpooyanfar/laravel-annotation
```

### Setup

In `App\Http\Controllers\Controller::class` :  
Replace `use Illuminate\Routing\Controller as BaseController;`  
With `use LaravelAnnotation\BaseController;`

### Middleware attribute

Here is an example that how you can use `Middleware` attribute in a laravel controller:

```php
<?php

use LaravelAnnotation\Attribute\Middleware;

#[Middleware(RedirectIfAuthenticated::class, 'sanctum', except: 'logout')]
class AuthController extends Controller
{
    public function register()
    {
    }
    
    public function login()
    {
    }
    
    #[Middleware(Authenticate::class, 'sanctum')]
    public function logout()
    {
    }
}
```
