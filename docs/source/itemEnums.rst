Item Enums
==========

Item Enums provide a convenient method of defining Permission and Role names, and provide code completion and checking.

Item Enums can be used with RBAM's :doc:`accessCheckerMiddleware` to provide access checking in route definitions,
and RBAM's :doc:`attributes`.

The Enum below defines RBAM Roles. The name of an Enum case is the name that is used in code,
the value is the name of the RBAC Item.

It uses `ItemTrait` to implement `ItemInterface`; the `getItemName()` method returns the fully qualified Item name.

The `Prefix` PHP Attribute defines a prefix to all RBAC Item names in the Enum, and a separator between the prefix
and Item names - the default separator is a space (' ').

.. code-block:: php

    namespace BeastBytes\Yii\Rbam\Rbac;

    use BeastBytes\Yii\Rbam\Rbac\Attribute\Prefix;

    #[Prefix('rbam', '.')]
    enum Role: string implements ItemInterface
    {
        use ItemTrait;

        case admin = 'admin';
        case itemManager = 'item.manager';
        case ruleManager = 'rule.manager';
        case userManager = 'user.manager';
    }

Example
-------

.. code-block:: php

    $itemName = Role::itemManager->getItemName(); // SitemName === 'rbam.item.manager'

.. _item-interface:
ItemInterface
-------------

Item Enums must implement ItemInterface to get the fully qualified Item name.
`\BeastBytes\Yii\Rbam\Rbac\ItemTrait` provides a concrete implementation.

API
+++

.. php:namespace:: BeastBytes\Yii\Rbam\Rbac

.. php:class:: ItemInterface

    `\BeastBytes\Yii\Rbam\Rbac\ItemTrait` provides a concrete implementation.

    .. php:method:: getItemName()

        Returns the fully qualified name of the RBAC Item - Permission or Role

        :returns: The fully qualified name of the RBAC Item
        :rtype: string

.. _prefix-attribute:
Prefix Attribute
----------------

.. php:namespace:: BeastBytes\Yii\Rbam\Rbac\Attribute

.. php:class:: Prefix

    .. php:method:: __construct($prefix, $separator)

        :param list<string>|string $prefix: Prefix for all Items in the Enum. If a list of strings they are concatenated using `$separator`
        :param string $separator: The separator between the prefix and the rest of the item name (default: ' ')

    Other methods are used internally to generate the fully qualified item name.
