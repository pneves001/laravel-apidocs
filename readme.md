# Laravel API documentation generating tool

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/pneves001/laravel-apidocs/tests.yml?label=tests)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/pneves001/laravel-apidocs)

> **Note:** This package is a fork of the original [johnylemon/laravel-apidocs](https://github.com/johnylemon/laravel-apidocs) package. It has been updated to support modern Laravel versions and enhanced with professional features like multiple API stacks, code snippets, and AI-ready exports.

### The problem
I don't like writing tons of lines of stupid annotations just to have hope that api documentation will be generated correctly without errors that says nothing. And I am not the only one. [More](WHY.md).

This package solves this problem the way I like - by writing PHP code.

### The solution

This package adds `apidocs` method to [Laravel](https://github.com/laravel/laravel) routes, where you can define route definitions using code you use every day.

**This way you can:**
 - Create professional, interactive API documentation.
 - **Multiple API Stacks**: Separate Public, Internal, and POS APIs easily.
 - **Webhooks Support**: Document callbacks and external system integrations.
 - **Global Authorization**: Set a token once for the entire session.
 - **Code Snippets**: Instant cURL and JavaScript snippets for every endpoint.
 - **Parameter Tables**: Clear, structured documentation of all request parameters.
 - **AI-Ready Export**: Optimized Markdown files for LLM consumption.
 - **Multiple Responses**: Document various scenarios for the same status code.

![img](screenshot.png)

## Getting started

 1. Add `pneves001/laravel-apidocs` repository

```bash
composer require pneves001/laravel-apidocs
```

2. Register `Pneves001\Apidocs\Providers\ApidocsServiceProvider` provider if not registered automatically.

3. Install package. This command will publish all required assets.
```bash
php artisan apidocs:install
```

4. **Enjoy!**


## Generating route documentation

This package ships with command for rapid route definition generation.

```bash
php artisan apidocs:endpoint SampleEndpoint
```
Brand new `SampleEndpoint` class will be placed within `app\Apidocs\Endpoints` directory.
Target directory may be changed within your `apidocs` config file.

This class contains only one `describe` method, where you have to use any of available methods that will describe your endpoint. Like that:

```php
<?php

namespace App\Apidocs\Endpoints;

use Pneves001\Apidocs\Endpoints\Endpoint;
use Pneves001\Apidocs\Facades\Param;

class SampleEndpoint extends Endpoint
{
    public function describe(): void
    {
        $this->title('List users')
            ->desc('Returns paginated list of users');
    }
}
```

## Generating webhook documentation

Webhooks are callbacks that your system sends to an external system. Since they don't have a local route endpoint, they have their own generation command.

```bash
php artisan apidocs:webhook UserCreatedWebhook
```

This will create a `UserCreatedWebhook` class in your `app\Apidocs\Endpoints` directory. It works exactly like a standard `Endpoint` class, but it's clearly marked as a webhook in the UI and markdown exports.

```php
<?php

namespace App\Apidocs\Endpoints;

use Pneves001\Apidocs\Endpoints\Webhook;
use Pneves001\Apidocs\Facades\Param;

class UserCreatedWebhook extends Webhook
{
    public function describe(): void
    {
        $this->title('User Created Callback')
            ->desc('This webhook is sent to your server when a new user is created.')
            ->method('POST')
            ->body([
                'user_id' => Param::int()->required(),
                'email' => Param::string()->required(),
            ]);
    }
}
```

## Advanced Features

### Multiple API Stacks
You can generate separate documentation for different parts of your system (e.g., Public API, Internal API).

**1. Configuration** in `config/apidocs.php`:
```php
'stacks' => [
    'internal' => [
         'uri' => '/apidocs/internal',
         'file_path' => storage_path('apidocs-internal.json'),
         'markdown_file_path' => storage_path('apidocs-internal.md'),
         'info' => [
             'title' => 'Internal API',
         ],
    ]
],
```

**2. Usage in Routes**:
```php
Route::get('/api/internal/users', 'InternalUserController@index')
     ->apidocs(InternalUserDoc::class, 'internal');
```

### Webhooks
Since webhooks are not attached to an application route, you can register them globally or in your routes file (e.g., `routes/apidocs.php`).

**1. Registration** using the helper function:
```php
apidocs_webhook(UserCreatedWebhook::class);
```

**2. Stack-Specific Webhooks**:
If you're using multiple stacks, you can pass the stack name as the second argument:
```php
apidocs_webhook(UserCreatedWebhook::class, 'internal');
```

**3. UI Display**:
Webhooks appear in a dedicated section in the sidebar and main view. To maintain documentation clarity, the "Try It" playground and code snippets are automatically disabled for webhooks, as they represent incoming requests to your customers' servers.

### Global Authorization
In the documentation UI, you'll find a **Bearer Token** input in the sidebar. Setting this once will:
- Automatically add the `Authorization: Bearer <token>` header to all "Try It" requests.
- Include the token in all generated code snippets.

### Code Snippets
Every endpoint automatically generates ready-to-use snippets for **cURL** and **JavaScript (Fetch)**. These snippets update dynamically based on your path parameters, query strings, and authorization token.

### AI-Ready Markdown Export
When you run `php artisan apidocs:generate`, the system generates a structured `.md` file alongside the JSON. This file is optimized for consumption by AI assistants, making it easy to provide full API context to tools like ChatGPT or Claude.

### Branding
Add your company logo by setting the `logo` key in `config/apidocs.php`. It will appear at the top of the sidebar.

---

### <a name="endpoint-available-methods"></a> Endpoint available methods

- [uri](#endpoint-uri)
- [method](#endpoint-method)
- [group](#endpoint-group)
- [deprecated](#endpoint-deprecated)
- [title](#endpoint-title)
- [description](#endpoint-description)
- [desc](#endpoint-desc)
- [query](#endpoint-query)
- [params](#endpoint-params)
- [body](#endpoint-body)
- [header](#endpoint-header)
- [headers](#endpoint-headers)
- [example](#endpoint-example)
- [examples](#endpoint-examples)
- [returns](#endpoint-returns)


#### <a name="endpoint-uri"></a> uri
Set uri. Called under the hood during endpoint mounting
```php
$this->uri('/users');
```

#### <a name="endpoint-method"></a> method
Set endpoint method. Called under the hood during endpoint mounting
```php
$this->method('POST');
```

#### <a name="endpoint-group"></a> group
Add endpoint to specific [group](#groups). Group have to be defined previously.
```php
$this->group('some-group-slug');
```

#### <a name="endpoint-deprecated"></a> deprecated
Will mark endpoint as deprecated.
```php
$this->deprecated();
```

#### <a name="endpoint-title"></a> title
Set endpoint title
```php
$this->title('Create user resource');
```

#### <a name="endpoint-description"></a> description
Set endpoint description
```php
$this->description('This endpoint contains logic for creating user resources based on provided data');
```

#### <a name="endpoint-desc"></a> desc
Alias for [description](#desc)

#### <a name="endpoint-query"></a> query
Defines endpoint query params. See: [parameters](#parameters)

```php
$this->query([
    'page' => Param::type('int')
])
```

#### <a name="endpoint-params"></a> params
Defines endpoint route params. See: [parameters](#parameters)

```php
$this->params([
    'id' => Param::int()->required()
])
```

#### <a name="endpoint-body"></a> body
Defines endpoint body params. See: [parameters](#parameters)

```php
$this->body([
    'name' => Param::string()->required(),
    'email' => Param::string()->required()
])
```

#### <a name="endpoint-header"></a> header
Defines endpoint header

```php
$this->header('x-pneves001', 'apidocs')
```

#### <a name="endpoint-headers"></a> headers
Defines multiple endpoint header at once

```php
$this->headers([
    'x-pneves001' => 'apidocs',
    'x-laravel' => 'framework'
])
```

#### <a name="endpoint-example"></a> example
Defines endpoint example. Optionally you can define example title

```php
$this->example([
    'name' => 'pneves001',
    'web' => 'https://pneves001.dev',
    'email' => 'hello@pneves001.dev'
], 'Store user')
```

#### <a name="endpoint-examples"></a> examples
Define multiple endpoint examples at once

```php
$this->examples([
    [
        'name' => 'johny',
        'web' => 'https://pneves001.dev',
        'email' => 'hello@pneves001.dev'
    ],
    [
        'name' => 'lemon',
        'web' => 'https://pneves001.dev',
        'email' => 'hello@pneves001.dev'
    ]
])
```

#### <a name="endpoint-returns"></a> returns
Define sample return value with status code. Optionally you may define response description.
You can call this multiple times for the same status code to show different scenarios in the UI tabs.

```php
$this->returns(201, [
    'name' => 'johny',
    'web' => 'https://pneves001.dev',
    'email' => 'hello@pneves001.dev'
], 'User created')
->returns(201, [
    'name' => 'Existing User',
], 'User already exists')
->returns(401, [
    'status' => 'unauthorized',
], 'Auth issue');
```

Additionally you can use methods like `returns201` (or any other status code)
```php
$this->returns201([
    'name' => 'johny',
    'web' => 'https://pneves001.dev',
    'email' => 'hello@pneves001.dev'
], 'User created');
```

## Endpoint definition usage

Okay, you created your first endpoint definition. Now it's time to use it as some real route definition.

Lets assume you have following routes:

```php
Route::get('api/users', [UsersController::class, 'index']);
```

If you want to use `App\Apidocs\Endpoints\SampleEndpoint` class as definition for first of them you should simply do this:

```php

use App\Apidocs\Endpoints\SampleEndpoint;

Route::get('api/users', [UsersController::class, 'index'])->apidocs(SampleEndpoint::class);

```

and... yes, thats it!

The only thing you have to do now is to call `php artisan apidocs:generate` command and visit `/apidocs` route to see it in action!

> :warning: **This package must clear route cache to generate apidocs properly.** If you are using route caching in your production environment remember to call `artisan route:cache` after `artisan apidocs:generate` command

### Resource routes

Sometimes you would like to use `resource` or `apiResource` methods to create bunch of typical CRUD endpoints. To specify definitions for these endpoints you have to use their names:

```php
Route::resource('posts', PostsController::class)->apidocs([
    'posts.index' => PostsIndexEndpoint::class,
    'posts.store' => PostStoreEndpoint::class,
]);
```

### <a name="parameters"></a>Parameters

You can define them using params, and pass them as array to `query`, `body` and `params` method when describing endpoint.

```php
use Pneves001\Apidocs\Facades\Param;

$this->query([
    // parameter name will be `page`
    'page' => Param::int()->example(1)->default(1)->optional()->description('Page number')
])
```

## <a name="groups"></a> Groups

Apidocs endpoints will be grouped. If no group is specified, default `non-groupped` group will be used.

You can define your own groups using `Pneves001\Apidocs\Facades\Apidocs` facade:

```php
use Pneves001\Apidocs\Facades\Apidocs;

Apidocs::defineGroup('users', 'Users', 'Manage users');
```

## Commands

| command                   | description           |
|---------------------------|-----------------------|
| `apidocs:install`         | install package       |
| `apidocs:generate`        | generate documentation|
| `apidocs:endpoint {name}` | create endpoint class |
| `apidocs:webhook {name}`  | create webhook class  |
| `apidocs:param {name}`    | create param class    |


## Testing
You can run the tests with:

```bash
vendor/bin/phpunit
```

## License
The MIT License (MIT)
Please see [LICENSE](LICENSE) for details.


## Contact

Visit me at [https://pneves001.dev](https://pneves001.dev)

---

Developed with ❤ by [pneves001](https://github.com/pneves001).
