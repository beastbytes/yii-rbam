# RBAC PHP Attributes
RBAM provides three (3) PHP Attributes:

* Prefix - for defining a prefix to all Item names in [Item Enums](item-enums.md)
* Permission - for defining a Permission in application code
* Role - for defining a Role in application code

See [Prefix Attribute](item-enums.md#prefix-attribute) for details of the `Prefix` attribute.

The rest of this section provides details of the `Permission` and `Role` attributes.

## Permission and Role Attributes
These attributes are used in application code to define Permissions at the method level and Roles at the class
level and are used by RBAM to initialise RBAC. The attributes use [Item Enums](item-enums.md).

Permission defined on class methods are children of Roles defined on the class.

## Example
::: info
For clarity, the example only shows the relevant `use` statements,
Attribute, class, and method definitions for the RbamController class, but not code for the methods.
:::

```php
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;

#[RoleAttribute(item: RbamRole::admin)]
final class RbamController {

    #[PermissionAttribute(
        item: RbamPermission::index,
        parent: [RbamRole::itemManager, RbamRole::ruleManager, RbamRole::userManager]
    )]
    public function index() {}

    #[PermissionAttribute(item: RbamPermission::clear)]
    public function clear() {}

    public function initialise() {}
}
```

The RbamController defines the `rbam.admin` Role - this is the Role name defined by the `RbamRole::admin` Enum case.

The `rbam.index` and `rbam.clear` Permissions - defined by `RbamPermission::index` and `RbamPermission::clear`
Enum cases - are children of the `rbam.admin` Role.

The `rbam.index` Permission is also a child of the `rbam.item.manager`, `rbam.rule.manager`, and `rbam.user.manager`
Roles - defined by the `RbamRole::itemManager`, `RbamRole::ruleManager`, and `RbamRole::userManager` Enum cases.

The `initialise()` method does not have a Permission associated with it; the method code determines whether or not RBAM
should be initialised.

Examine the controllers in RBAM's source code for more examples.

# API
## Namespace: BeastBytes\Yii\Rbam\Rbac\Attribute

## Class: Permission|Role

An attribute that defines a Permission or Role to apply to a controller method or class.

Permission attributes are applied to controller methods; there can only be one Permission attribute applied to a method.

Role attributes are applied to controller classes; there may be multiple Role attributes applied to a class.

### Methods

__construct($item, $description = null, $parent = [], $ruleName = null)

*Parameter*: **ItemInterface $item**: An Item Enum case

*Parameter*: **?string $description**: The description of the Item. If `NULL` the description is generated from *$item*;
the format is {itemName}{separator}{ItemEnum::DESCRIPTION} (default: null)

*Parameter*: **ItemInterface|list&lt;ItemInterface&gt; $parent**: The parent(s) of the Item

*Parameter*: **?string $ruleName**: The name of a rule to be applied to the Item (default: null)

::: info
Other methods are used internally by RBAM to initialise RBAC
:::