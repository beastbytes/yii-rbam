# Role Based Access Manager (RBAM)
The Role Based Access Manager (RBAM) package simplifies defining, managing, and using RBAC in Yii3 applications.

The package contains a number of components:

## Package Components
### Web UI
The Web UI simplifies creating and managing a project's RBAC Roles, Permissions, Rules, hierarchy,
and user Role assignments.

See [Web UI](web-ui/web-ui)

### Access Checker Middleware
The package also contains access checker middleware that can be used in route definitions to determine if
the current user is permitted to access the requested resource.

See [Access Checker Middleware](src/middleware/access-checker)

### PHP Attributes
The package defines PHP Attributes that can be used in application source code to define RBAC Permissions and Roles.
Using the Attributes makes Permissions and Roles visible in source code, and allows RBAM to initialise RBAC for the
application.

See [PHP Attributes](attributes.md)

## Unsupported Features
### Composite Rules
Composite rules are not currently supported.
