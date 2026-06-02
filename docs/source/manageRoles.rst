Manage Roles
============

Managing Roles consists of creating, updating, and deleting Roles.

Roles
-----

The Roles home page contains a button to create a new Role and a paged list of Roles;
the following information is given for each Role:

* Name of the Role
* Description of the Role - either as entered or in the current locale (see :doc:`i18n`)
* Name of the Role(s) that grant the Role
* Date and time the Role was created
* Date and time the Role was last updated
* Buttons to `View`, `Update`, or `Remove` the Role

.. image:: /_images/roles_index.png
Roles

View a Role
-----------

The Role view page contains buttons to update the Role and manage translations,
details of the Role, and - tabs - a :doc:`hierarchyDiagram` showing ancestors and children,
a list of `Child Roles`, a list of `Permissions` granted by the role, and a list of users `Assigned` the role.

The `Child Roles` tab contains a button to manage child Roles of the current Role.

The details shown for each Role are:

* Name - Role name translated to the current locale
* Description - Role description translated to the current locale
* Rule - Name of the Rule, if any, applied to the Role
* Created - Date and time the Role was created
* Updated - Date and time the Role was last updated

Default Roles
+++++++++++++

If the application defines any default roles they are shown with For All symbol (∀)
in a green circle after the name.

Guest Role
++++++++++

If the application defines a Guest Role it shown with the Empty Set symbol (∅) in a blue circle after the name.

Manage Child Roles
------------------

The page contains a list of Roles that are currently children, and a list of Roles that are not.

Roles that are currently children can be removed.

.. note::

    The child Role is not removed from RBAC; only the parent - child relationship is removed.

Roles that are not currently children can be added.

Manage Permissions
------------------

The page contains a list of Permissions that are granted by the Role, and a list of Permissions that are not.

Permissions that are directly granted by the role can be removed.

.. note::

    The Permission is not removed from RBAC; only the parent - child relationship is removed.

.. note::

    Permissions granted by child roles can not be removed; they must be removed from the Role that directly grants them.

Permissions that are not currently children can be added.

Translations
------------

Translations are listed by locale. To translate a locale, click on the locale to expand it and complete the form.

.. note::

    If either the Name or Description field for a locale is left empty it will not be translated for that locale.

Repeat for all required locales then click `Submit`.

If translations are used and either the name and/or description are changed,
the current translations are moved to the new name and/or description.
If the translations themselves require updating this must be done after updating the Role.

Create a Role
-------------

To create a Role, click the `Create` button on the Roles index page then
complete the form. The form has the following fields:

* Name - The name of the Role - Required
* Description - Description of the Role - Optional, *must* be left blank if using translations
* Rule - The Rule to be applied to the Role - Optional

Update a Role
-------------

The fields and requirements are the same as those for creating a Role.

If translations are used and either the name and/or description are changed,
the current translations are moved to the new name and/or description.
If the translations themselves require updating this must be done after updating the Role.

Remove a Role
-------------

To remove a Role, click the Remove button for the Role and confirm the removal in the dialog.
The Role will be removed from RBAC.

Parent-child relationships, assignments, and translations are also removed.

.. warning::

    Removing a Role *may* result in orphaned child Roles and Permissions.
    The RBAC hierarchy should be checked following Role removal.