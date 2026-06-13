# BeastBytes\Yii\Rbam\Rbac\Attribute\Prefix

Defines a prefix applied to all items defined in an Item Enum

## Public Methods

| Method                        | Description | Defined By                                                      |
|-------------------------------|-------------|-----------------------------------------------------------------|
| [__construct()](#__construct) |             | [BeastBytes\Yii\Rbam\Rbac\Attribute\Prefix](./prefix-attribute) |

## Method Details

### __construct()

<table>
    <thead>
        <tr>
            <th colspan="3">public <a href="https://www.php.net/language.types.string" target="_blank">string</a> __construct(<a href="https://www.php.net/language.types.array" target="_blank">array</a>|<a href="https://www.php.net/language.types.string" target="_blank">string</a> $prefix, <a href="https://www.php.net/language.types.string" target="_blank">string</a> $separator = ' ')</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td>return</td>
            <td><a href="https://www.php.net/language.types.string" target="_blank">string</a></td>
            <td>The name of the user</td>
        </tr>
    </tfoot>
    <tbody>
        <tr>
            <td>$prefix</td>
            <td><a href="https://www.php.net/language.types.string" target="_blank">string</a>[]|<a href="https://www.php.net/language.types.string" target="_blank">string</a></td>
            <td>Prefix for all Items in the Enum. If a list of strings will be concatenated using $separator</td>
        </tr>
        <tr>
            <td>$separator</td>
            <td><a href="https://www.php.net/language.types.string" target="_blank">string</a></td>
            <td>The separator between the prefix and the rest of the item name</td>
        </tr>
    </tbody>
</table>
