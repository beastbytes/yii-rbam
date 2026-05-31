<?php

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var ItemForm $formModel
 * @var RbamParameters $rbamParameters
 * @var array<string, string> $ruleClasses
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 * @var string $type
 */

use BeastBytes\Yii\Rbam\Item\ItemForm;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle($translator->translate(
    sprintf(($formModel->isCreate() ? 'header.%s.create' : 'header.%s.update'), $type)
));

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: 'label.' . $type . 's', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.item.index', ['type' => $type . 's']),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);

$tabIndex = 1;
?>

<h2 class="header"><?= $this->getTitle() ?></h2>

<?= Html::form()
    ->post($urlGenerator->generateFromCurrent([]))
    ->csrf($csrf)
    ->id('form-item')
    ->open()
?>
<?= Field::errorSummary($formModel) ?>
<?= Field::text($formModel, 'name')
    ->autofocus(true)
    ->required(true)
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container')
    ->addInputClass('form-input')
    ->pattern(ItemForm::NAME_REGEX)
    ->addLabelClass('form-label')
    ->afterInput(Html::span())
    ->tabindex($tabIndex++)
?>
<?= Field::text($formModel, 'description')
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->afterInput(Html::span())
    ->tabindex($tabIndex++)
?>
<?= Field::select($formModel, 'ruleName', [
    'prompt()' => [$translator->translate(id: 'prompt.select-rule', category: 'rbam')],
    'optionsData()' => [$ruleClasses],
])
    ->containerClass('form-control-container')
    ->addContainerClass(empty($ruleNames) ? 'disabled' : '')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->disabled(empty($ruleClasses))
    ->tabindex($tabIndex++)
?>
<div class="form-buttons">
    <?= Field::submitButton()
         ->containerClass('form-button')
         ->buttonClass($rbamParameters->getButtons('submit')['attributes']['class'])
         ->buttonId('submit-button')
         ->tabindex($tabIndex++)
         ->content($translator->translate(id: $rbamParameters->getButtons('submit')['content'], category: 'rbam'))
    ?>
    <?= Field::button()
        ->containerClass('form-button')
        ->buttonAttributes([
            'onClick' => sprintf(
                'window.location.href = "%s"',
                $urlGenerator->generate('rbam.item.index', ['type' => $type . 's'])
            )
        ])
        ->buttonClass($rbamParameters->getButtons('cancel')['attributes']['class'])
        ->tabindex($tabIndex)
        ->content($translator->translate(id: $rbamParameters->getButtons('cancel')['content'], category: 'rbam'))
    ?>
</div>
<?= Html::form()
    ->close()
?>