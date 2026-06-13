# Interface BeastBytes\Yii\Rbam\User\UserRepositoryInterface

<table>
    <tr>
        <th>Implemented By</th>
        <td>An application class <b><i>must</i></b> implement this interface</td>
    </tr>
</table>

An interface to count and return user objects.

## Public Methods

| Method                    | Description                          | Defined By                                           |
|---------------------------|--------------------------------------|------------------------------------------------------|
| [count()](#count)         | Returns the number of users          | [BeastBytes\Yii\Rbam\User\UserRepositoryInterface](./user-repository-interface) |
| [findAll()](#findall)     | Returns all users                    | [BeastBytes\Yii\Rbam\User\UserRepositoryInterface](./user-repository-interface) |
| [findByIds()](#findbyids) | Returns the users with the given IDs | [BeastBytes\Yii\Rbam\User\UserRepositoryInterface](./user-repository-interface) |

## Method Details

### count()
Returns the number of users

<table>
    <thead>
        <tr>
            <th colspan="3">public <a href="https://www.php.net/language.types.int" target="_blank">int</a> count()</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td>return</td>
            <td><a href="https://www.php.net/language.types.int" target="_blank">int</a></td>
            <td>The number of users</td>
        </tr>
    </tfoot>
</table>

### findAll()
Returns all users

<table>
    <thead>
        <tr>
            <th colspan="3">public <a href="./user-interface">BeastBytes\Yii\Rbam\User\UserInterface</a>[] findAll()</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td>return</td>
            <td><a href="./user-interface">BeastBytes\Yii\Rbam\User\UserInterface</a>[]</td>
            <td>All users</td>
        </tr>
    </tfoot>
</table>

### findByIds()
Returns users with the given IDs

<table>
    <thead>
        <tr>
            <th colspan="3">public <a href="./user-interface">BeastBytes\Yii\Rbam\User\UserInterface</a>[] findByIds()</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td>return</td>
            <td><a href="./user-interface">BeastBytes\Yii\Rbam\User\UserInterface</a>[]</td>
            <td>All users</td>
        </tr>
    </tfoot>
    <tbody>
        <tr>
            <td>$ids</td>
            <td><a href="https://www.php.net/language.types.string" target="_blank">string</a>[]</td>
            <td>Array of IDs</td>
        </tr>
    </tbody>
</table>
