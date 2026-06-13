# BeastBytes\Yii\Rbam\Rbac\ItemInterface

<table>
    <tr>
        <th>Implemented By</th>
        <td><a href="../item-enums">Item Enums</a></td>
    </tr>
</table>

An interface to get the fully qualified Item name and a generated description from an Item Enum.

## Public Methods

| Method  | Description   | Defined By    |
|---------------------------------------------|-----------------------|------------------|
| [getItemDescription()](#getitemdescription) | Returns a generated description of the RBAC item defined by an Item Enum | [BeastBytes\Yii\Rbam\Rbac\ItemInterface](./item-interface) |
| [getItemName()](#getitemname)               | Returns the fully qualified name of the RBAC item defined by an Item Enum | [BeastBytes\Yii\Rbam\Rbac\ItemInterface](./item-interface) |

## Method Details

### getItemDescription()
Returns a generated description of the RBAC item defined by an Item Enum

<table>
    <thead>
        <tr>
            <th colspan="3">public <a href="https://www.php.net/language.types.string" target="_blank">string</a> getItemDescription()</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td>return</td>
            <td><a href="https://www.php.net/language.types.string" target="_blank">string</a></td>
            <td>A generated description of the RBAC item defined by an Item Enum</td>
        </tr>
    </tfoot>
</table>

### getItemName()
Returns the fully qualified name of the RBAC item defined by an Item Enum

<table>
    <thead>
        <tr>
            <th colspan="3">public <a href="https://www.php.net/language.types.string" target="_blank">string</a> getItemName()</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td>return</td>
            <td><a href="https://www.php.net/language.types.string" target="_blank">string</a></td>
            <td>The fully qualified name of the RBAC item defined by an Item Enum</td>
        </tr>
    </tfoot>
</table>
