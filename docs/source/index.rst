Role Based Access Manager (RBAM)
================================

Role Based Access Manager (RBAM) is a web UI for
the `Yii3 Role Based Access Control (RBAC) <https://github.com/yiisoft/rbac>`__ package
that greatly simplifies creating and managing a project's RBAC Roles, Permissions, Rules, hierarchy,
and user Role assignments.

The package also contains access checker middleware that can be used in route definitions to determine if
the current user is permitted to access the requested resource, PHP Attributes to define application
RBAC Permissions, Roles, and hierarchy, and support for using Enums to define RBAC Permission and Role names.

Features
--------

* Create, edit, and delete RBAC Permissions, Roles, and Rules
* Create and edit RBAC hierarchy
* Manage assignment of Roles to users
* Manage assignment of Rules to Permissions and Roles
* RBAC hierarchy visualisation
* RBAC initialisation
* PHP attributes to define application RBAC Permissions, Roles, and hierarchy
* Support for Enums to define RBAC Permission and Role names
* Access checker middleware
* I18n

Unsupported Features
--------------------

Composite Rules
+++++++++++++++

Composite rules are not currently supported.

.. seealso::

    `Yii3 RBAC package <https://github.com/yiisoft/rbac>`__

    `Yii3 RBAC documentation <https://github.com/yiisoft/docs/blob/master/guide/en/security/authorization.md#role-based-access-control-rbac->`__

    `Wikipedia RBAC page <https://en.wikipedia.org/wiki/Role-based_access_control>`__

.. toctree::
    :maxdepth: 2
    :caption: Contents

    installation
    configuration
    gettingStarted
    usage
    tips
