Getting Started
===============

Prerequisite
------------

There are two prerequisites for RBAM to integrate into an application:

1. There is a class that implements RBAM's `UserRepositoryInterface` that returns User objects
2. The User objects returned from the UserRepositoryInterface implement RBAM's `UserInterface`

UserRepositoryInterface
+++++++++++++++++++++++

.. php:namespace:: BeastBytes\Yii\Rbam\User

.. php:class:: UserRepositoryInterface

    An interface to count and return user objects

    .. php:method:: count()

        :returns: A count of users
        :rtype: int

    .. php:method:: findAll()

        :returns: All users
        :rtype: list<UserInterface>

    .. php:method::

        :param list<string> $ids: The IDs of the users to find
        :returns: Users with the given IDs
        :rtype: list<UserInterface>

UserInterface
+++++++++++++

.. php:namespace:: BeastBytes\Yii\Rbam\User

.. php:class:: UserRepositoryInterface

    An interface to get a user's name

    .. php:method:: getName()

        :returns: The user's name
        :rtype: string

RBAC Initialisation
-------------------

Before RBAM can be used, RBAC must be initialised. As a minium, RBAM's RBAC items and hierarchy must be initialised,
however RBAM can also initialise application RBAC items if it uses RBAM's Permission and Role PHP Attributes.

Initialisation is from RBAM's initialisation page. Navigate to `/rbam/initialise` and enter the user ID of the user
to be assigned the `rbam.admin` role; this role allows the specified user to perform all RBAM actions.

.. note::

    RBAM prevents initialisation if there are any RBAC items defined.
    This ensures RBAC can not accidentally re-initialised.

Initialising Application RBAC Items
-----------------------------------

For RBAM to be able to initialise the application's RBAC items and hierarchy it *must* use
RBAM's Permission and Role PHP Attributes. If they are not used, application RBAC items and hierarchy can be set up
manually using RBAM.

If RBAM is to initialise the application's RBAC items, specify the following:

* Source Directory - The path to the application source directory relative to the application root directory
* Except - The pattern(s) to exclude directories from being inspected
* Only - The pattern(s) to define the files that should be inspected

.. note::

    See `Path Matcher documentation <https://github.com/yiisoft/files/tree/master#path-matchers>`__
    for details of how specify `Except` and `Only`.
    In both case, multiple entries can be given using a comma separates list.

Initialising
------------

Once the form is complete, click `Submit` to initialise RBAC.

Assuming that the current user is assigned the `rbam.admin` role, the :doc:`rbamDashboard` is displayed.
