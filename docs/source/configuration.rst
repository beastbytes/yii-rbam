Configuration
=============

Buttons
-------

The buttons in RBAM can be configured in the `$params['beastbytes/yii-rbam']['buttons']`.

The HTML attributes and content for each button can be configured.

The HTML attributes allow integration with application CSS using classes; the defaults use RBAM's CSS.

The content defines the text or icon for each button; the defaults define keys for RBAM's messages that can be
translated to different locales.

Datetime Format
---------------

The format for created and update datetimes is defined in `$params['beastbytes/yii-rbam']['datetimeFormat']`.
The format must be a format recognised by the PHP DateTime class. The default is `'Y-m-d H:i:s'`.

Hierarchy Diagram Styles
------------------------

The colour and font size of the hierarchy diagram elements can be configured in
`$params['beastbytes/yii-rbam']['diagramStyles']`

Page Size
---------

The page size for main lists of items can be configured in `$params['beastbytes/yii-rbam']['pageSize']`;
the default is 20.

Tab Page Size
-------------

The page size for lists of items in tabs can be configured in `$params['beastbytes/yii-rbam']['tabPageSize']`;
the default is 10.

Default and Guest Roles
-----------------------

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