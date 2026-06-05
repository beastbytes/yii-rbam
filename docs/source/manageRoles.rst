Manage Roles
============

Managing Roles consists of creating, updating, and deleting Roles.

Roles
-----

The Roles home page contains a button to create a new Role and a paged list of Roles;
the following information is given for each Role:

* Name of the Role
* Description of the Role - translated to the current locale (see :doc:`i18n`)
* Name of the Rule, if any, that applies to the Role
* Date and time the Role was created
* Date and time the Role was last updated
* Buttons to `View`, `Update`, or `Remove` the Role

Default Roles
+++++++++++++

If the application defines any default Roles they are shown with the `For All` symbol (∀)
in a green circle after the name.

Guest Role
++++++++++

If the application defines a Guest Role it shown with the `Empty Set` symbol (∅) in a blue circle after the name.

RBAM Roles
++++++++++

RBAM Roles are shown with the `Part` symbol (∂) in a red circle after the name.

.. image:: /_images/role_index.png

Roles Index

View a Role
-----------

The Role view page contains buttons to update the Role and manage translations,
details of the Role, and - in tabs - a :doc:`hierarchyDiagram` showing ancestors and descendants,
a list of `Child Roles`, a list of `Permissions` granted by the role, and a list of users `Assigned` the role.

The `Child Roles` tab contains a button to manage child Roles of the current Role.

The details shown for each Role are:

* Name - Role name
* Description - Role description - both raw and translated to the current locale
* Rule - Name of the Rule, if any, applied to the Role
* Created - Date and time the Role was created
* Updated - Date and time the Role was last updated

.. image:: /_images/role_view.png

Role View

Manage Child Roles
------------------

To see a list of Child Roles, click the `Child Roles` tab.

To manage Child Roles, click the `Manage Child Roles` button.

The page contains a list of Roles that are currently children, and a list of Roles that are not.

Roles that are currently children can be removed.

.. note::

    The Child Role is not removed from RBAC; only the parent - child relationship is removed.

Roles that are not currently children can be added.

.. note::

    Not all Roles can be added as a Child Role.

    Default Roles, the Guest Role, and Roles that would create a circular reference can not be added as a Child Role;
    these Roles do not have an `Add` button.

.. image:: /_images/role_manage_child_roles.png

Manage Child Roles

Manage Permissions
------------------

To see a list of Permissions granted by the Role, click the `Permissions` tab. The list includes Permissions that are
granted directly, i.e. they are direct children of the Role,
and Permissions granted indirectly, i.e. by descendant Roles or Permissions.

To manage Permissions granted by the Role, click the `Manage Permissions` button,

The page contains a list of Permissions that are granted by the Role, and a list of Permissions that are not.

Permissions that are directly granted by the role can be removed by clicking the `Remove` button for that Permission,
or all Permissions can be removed by clicking the `Remove All` button. In both cases a confirmation dialog is shown.

.. note::

    The Permission is not removed from RBAC; only the parent - child relationship is removed.

.. note::

    If the Permission being removed has descendant Permissions they will also be removed from the Role,
    but their child - parent relationships are kept.

Permissions that are not currently granted by the Role can be added.

.. note::

    If the Permission being added has descendant Permissions they will also be added to the list of Permissions granted
    by the Role.

.. image:: /_images/role_manage_permissions.png

Manage Permissions

Assignments
-----------

To see a list users assigned the Role, click the `Assignments` tab.

.. note::

    Role Assignments are not managed from the tab; they are managed from the User view.

Translations
------------

To create or update translations for the Role description, click the `Translations` button.

Translations are listed by locale. To translate a locale, click on the locale to expand it and enter the translations.

Repeat for all required locales then click `Submit`.

.. note::

    The translation may not immediately show in the Role view; this is due to the way Yii caches translations.

    Refresh the Role view to see the translation.

.. image:: /_images/role_translate.png

Translate Role

Create a Role
-------------

To create a Role, click the `Create` button on the Roles index page then
complete the form. The form has the following fields:

* Name - The name of the Role - Required
* Description - Description of the Role - Required
* Rule - The Rule to be applied to the Role - Optional

.. image:: /_images/role_create.png

Create a Role

Update a Role
-------------

The fields and requirements are the same as those for creating a Role.

.. note::

    The name and description fields for RBAM Roles can not be edited.


If translations are used and the description is changed the current translations are moved to the new description.
If the translations themselves require updating this must be done after updating the Role.

.. image:: /_images/role_update.png

Update a Role

Remove a Role
-------------

To remove a Role, click the Remove button for the Role and confirm the removal in the dialog.
The Role will be removed from RBAC.

Parent-child relationships, assignments, and translations are also removed.

.. warning::

    Removing a Role *may* result in orphaned child Roles and Permissions.
    The RBAC hierarchy should be checked following Role removal.