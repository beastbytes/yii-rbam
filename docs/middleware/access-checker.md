# Access Checker Middleware
RBAM's Access Checker middleware can be used in route definitions to determine whether the current user
has permission to perform the action associated with the route.

The Access Checker middleware uses the configured `AccessCheckerInterface` - typically `Yiisoft\\Rbac\\Manager` -
to check that the current user has permission. If the user does not have permission,
it returns a response with staus 403 (Forbidden) and optionally a redirect header.

## Configuration
The AccessChecker can be configured to include a redirect header in the response when the current user does not have the
required permission.

```php
return [
    \BeastBytes\Yii\Rbam\Middleware\AccessChecker::class => [
        'withRoute()' => [{redirect route}]
    ],
];
```

## Using in a Route Definition
```php
Route::methods([Method::GET, Method::POST], '{pattern}')
    ->name('{route name}')
    ->middleware(fn(AccessChecker $checker) => $checker->withPermission({permission}))
    ->action({action})
,
```

The Permission ({permission}) can be either a string - the Permission name,
or an instance of [Item Interface](../item-enums#item-interface).

If the redirect route has not been configured as above, or a different redirect route is required,
this can be added in the route definition:

```php
Route::methods([Method::GET, Method::POST], '{pattern}')
    ->name('{route name}')
    ->middleware(fn(AccessChecker $checker) => $checker
        ->withPermission({permission})
        ->withRoute({redirect route})
    )
    ->action({action})
,
```

## AccessChecker API

Namespace: BeastBytes\Yii\Rbam\Middleware

Class: AccessChecker

Implements: Psr\\Http\\Server\\MiddlewareInterface

### Methods
<pre>
withPermission($permission)

    Defines the permission to check.

    param: BeastBytes\\Yii\\Rbam\\Rbac\\Permission|string $permission: The permission to check. Either a Permission enum  or the name of the RBAC permission
    returns: A new instance of the access checker with the permission to check
    return type: AccessChecker
</pre>
<pre>
withRoute(string $route, array $arguments = [], array $queryParameters = [], ?string $hash = null)

    Defines the route to redirect to if access is denied.
    The parameters are the same as those for Yiisoft\Router\UrlGeneratorInterface

    param string $name: Name of the route
    param array $arguments: Argument-value set. Unused arguments will be moved to query parameters if a query parameter with the name doesn't exist
    param array $queryParameters: Parameter-value set
    param ?string $hash: Hash part (fragment identifier) of the URL
    returns: A new instance of the access checker with the redirect route
    return type: AccessChecker
</pre>