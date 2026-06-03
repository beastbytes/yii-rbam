Prerequisites
=============

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

    .. php:method:: findByIds($ids)

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

