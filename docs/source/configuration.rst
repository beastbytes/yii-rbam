Configuration
=============

include RBAM routes in application routes


Defining Default and Guest Roles
--------------------------------

If the application uses Default and/or Guest Roles, they are defined when configuring the RBAC Manager.

The example below uses the Default roles defined in `$params['yiisoft/rbac']['defaultRoles']`
and the Guest role in `$params['yiisoft/rbac']['guestRole']`.

The definition of Default Roles is `list<array{name: string, description: string}>`.

The definition of Guest Role is `array{name: string, description: string}`.

.. code-bloc:: php

    ManagerInterface::class => [
        'class' => Manager::class,
        'setDefaultRoleNames()' => [array_map(
            fn(array $defaultRole): string => $defaultRole['name'],
            $params['yiisoft/rbac']['defaultRoles']
        )],
        'setGuestRoleName()' => [$params['yiisoft/rbac']['guestRole']['name']],
    ],