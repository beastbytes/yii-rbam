Application Integration
=======================

There are some prerequisites for RBAM to integrate into an application:

UserRepositoryInterface
-----------------------

There is a class that implements RBAM's `UserRepositoryInterface` that returns User objects.

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
-------------

The User objects returned from the UserRepositoryInterface implement RBAM's `UserInterface`

.. php:namespace:: BeastBytes\Yii\Rbam\User

.. php:class:: UserRepositoryInterface

    An interface to get a user's name

    .. php:method:: getName()

        :returns: The user's name
        :rtype: string

Application Layout
------------------

RBAM provides a header block named `rbam-header`; it contains breadcrumbs and the menu on the RBAM dashboard.

To integrate this into the application layout, the `body` tag should look similar to:

.. code-block:: php

    <body>
    <?php $this->beginBody() ?>
        <?php if ($this->hasBlock('rbam-header')): ?>
            <?= $this->getBlock('rbam-header') ?>
        <?php else: ?>>
            <?= $this->render('./header') ?>
        <?php endif; ?>

        <div class="content">
            <?= $content ?>
        </div>

        <?= $this->render('./footer') ?>
    <?php $this->endBody() ?>
    </body>

This checks for the `rbam-header` block; if the block exists (an RBAM page is being rendered) it is rendered,
if not a default header is rendered.

Styling
-------

RBAM provides a default set of CSS styles in `yii-rbam/resources/assets/rbam.css`.

All RBAM HTML elements are in a container element that has the `rbam` class.
This isolates RBAM styles and simplifies overriding them if required.