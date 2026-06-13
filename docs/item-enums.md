# Item Enums

Item Enums provide a convenient method of defining Permission and Role names, and provide code completion and checking.

::: warning
Yii's RBAC documentation describes using string backed enums to define RBAC items; this *will not* work in RBAM.

RBAM requires Item Enums to implement [Item Interface](#item-interface).
:::

Item Enums can be used with RBAM's [Access Checker Middleware](./middleware/access-checker) to provide access checking in route definitions,
and RBAM's [PHP Attributes](./attributes).

ItemEnums can have a private constant named `DESCRIPTION` which is used if the item's description is generated
(as opposed to being specified in the PHP Attribute).

The Enum below defines RBAM Roles. The name of an Enum case is the name that is used in code,
the value is the name of the RBAC Item.

It uses `ItemTrait` to implement `ItemInterface`; the `getItemName()` method returns the fully qualified Item name.

The `Prefix` PHP Attribute defines a prefix to all RBAC Item names in the Enum, and a separator between the prefix
and Item names; the default separator is a space (' ').

```php
namespace BeastBytes\Yii\Rbam\Rbac;

use BeastBytes\Yii\Rbam\Rbac\Attribute\Prefix;

#[Prefix('rbam', '.')]
enum Role: string implements ItemInterface
{
    use ItemTrait;

    private const string DESCRIPTION = 'description';

    case admin = 'admin';
    case itemManager = 'item.manager';
    case ruleManager = 'rule.manager';
    case userManager = 'user.manager';
}
```

## Example
```php
$itemName = Role::itemManager->getItemName(); // itemName === 'rbam.item.manager'
```

## Item Interface
Item Enums must implement [ItemInterface](./api/item-interface) to get the fully qualified Item name.
[ItemTrait](./api/item-trait) provides a concrete implementation.
