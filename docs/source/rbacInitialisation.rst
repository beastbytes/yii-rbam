RBAC Initialisation
===================

Before RBAM can be used, RBAC must be initialised. As a minium, RBAM's RBAC items and hierarchy must be initialised,
however RBAM can also initialise application RBAC items if it uses RBAM's :doc:`attributes`
to define Permissions and Roles.

Initialisation is from RBAM's initialisation page. Navigate to `/rbam/initialise` and enter the user ID of the user
to be assigned the `rbam.admin` role; this role allows the specified user to perform all RBAM actions.

.. note::

    RBAM prevents initialisation if there are any RBAC items defined.
    This ensures RBAC can not accidentally re-initialised.

Initialising Application RBAC Items
-----------------------------------

For RBAM to initialise the application's RBAC items and hierarchy it *must* use
RBAM's :doc:`attributes` to define Permissions and Roles.
If they are not used, application RBAC items and hierarchy can be created manually using RBAM.

If RBAM is to initialise the application's RBAC items, specify the following:

* Source Directory - The path to the application source directory relative to the application root directory
* Except - The pattern(s) to exclude directories from being inspected
* Only - The pattern(s) to define the files that should be inspected

.. note::

    See `Path Matcher documentation <https://github.com/yiisoft/files/tree/master#path-matchers>`__
    for details of how specify `Except` and `Only`.
    In both case, multiple entries can be given using a comma separates list.

.. note::

    It is assumed that RBAM is in the `{root}/vendor` directory.

Initialising
------------

Once the form is complete, click `Submit` to initialise RBAC.

Assuming that the current user is assigned the `rbam.admin` role, the :doc:`rbamDashboard` is displayed.
