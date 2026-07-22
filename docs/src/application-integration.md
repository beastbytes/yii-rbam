# Application Integration
There are some prerequisites for RBAM to integrate into an application:

* There __*must*__ be class that implements [BeastBytes\Yii\Rbam\User\UserRepositoryInterface](src/api/user-repository-interface)
* Objects returned by [BeastBytes\Yii\Rbam\User\UserRepositoryInterface](src/api/user-repository-interface) __*must*__ implement [BeastBytes\Yii\Rbam\User\UserInterface](src/api/user-interface)

# Application Layout
RBAM provides a header block named `rbam-header`; it contains breadcrumbs and the menu on the RBAM dashboard.

To integrate this into the application layout, the `body` tag should look similar to:

```php
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
```

This checks for the `rbam-header` block; if the block exists (an RBAM page is being rendered) it is rendered,
if not a default header is rendered.

# Styling
RBAM provides a default set of CSS styles in `yii-rbam/resources/assets/rbam.css`.

All RBAM HTML elements are in a container element that has the `rbam` class.
This isolates RBAM styles and simplifies overriding them if required.