<?php

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var FieldFactory $fieldFactory
 * @var ItemForm $formModel
 * @var RbamParameters $rbamParameters
 * @var array<string, string> $ruleClasses
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 * @var string $type
 */

use BeastBytes\Yii\Rbam\Alpine\FieldFactory;
use BeastBytes\Yii\Rbam\Item\ItemForm;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncode;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle($translator->translate(id: 'header.rbac.initialise', category: 'rbam'));

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
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
<?= $fieldFactory->errorSummary($formModel)
    ->containerClass('form-error')
?>
<?= $fieldFactory->text($formModel, 'userId')
    ->autofocus(true)
    ->required(true)
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container')
    ->inputInvalidClass('invalid')
    ->inputValidClass('valid')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->hintClass('form-hint')
    ->afterInput(Html::span())
    ->template("{label}\n{input}\n{error}\n{hint}")
    ->tabindex($tabIndex++)
?>
<?= $fieldFactory->fieldset()
    ->containerAttributes([
        'x-data' => '{initialiseApplication: false}',
        'x-init' => "initialiseApplication = document.getElementById('initialiseform-initialiseapplication').checked",
    ])
    ->containerClass('border-t mt-1 pt-1')
    ->legend($translator->translate(id: 'label.application', category: 'rbam'))
    ->begin();
?>
    <?= Html::div(NoEncode::string($translator->translate(id: 'message.initialise', category: 'rbam'))) ?>
    <?= $fieldFactory->toggle($formModel, 'initialiseApplication')
        ->containerClass('form-control-container')
        ->addLabelClass('form-label')
        ->inputContainerTag('div')
        ->inputContainerClass('form-input-container')
        ->inputAttributes(['x-model' => 'initialiseApplication'])
        ->hintClass('form-hint')
        ->tabindex($tabIndex++)
    ?>
    <?= Html::openTag('div', ['x-show' => 'initialiseApplication', 'x-transition' => true]) ?>
        <?= Html::div(NoEncode::string($translator->translate(
                id: 'message.initialise-application',
                category: 'rbam'
            )))
        ?>
        <?= $fieldFactory->text($formModel, 'srcDir')
            ->autofocus(true)
            ->required(true)
            ->containerClass('form-control-container')
            ->inputContainerTag('div')
            ->inputContainerClass('form-input-container')
            ->inputInvalidClass('invalid')
            ->inputValidClass('valid')
            ->addInputClass('form-input')
            ->addLabelClass('form-label')
            ->errorClass('form-error')
            ->hintClass('form-hint')
            ->afterInput(Html::span())
            ->template("{label}\n{input}\n{error}\n{hint}")
            ->tabindex($tabIndex++)
        ?>
        <?= $fieldFactory->text($formModel, 'except')
            ->autofocus(true)
            ->required(true)
            ->containerClass('form-control-container')
            ->inputContainerTag('div')
            ->inputContainerClass('form-input-container')
            ->addInputClass('form-input')
            ->inputInvalidClass('invalid')
            ->inputValidClass('valid')
            ->addLabelClass('form-label')
            ->errorClass('form-error')
            ->hintClass('form-hint')
            ->afterInput(Html::span())
            ->template("{label}\n{input}\n{error}\n{hint}")
            ->tabindex($tabIndex++)
        ?>
        <?= $fieldFactory->text($formModel, 'only')
            ->autofocus(true)
            ->required(true)
            ->containerClass('form-control-container')
            ->inputContainerTag('div')
            ->inputContainerClass('form-input-container')
            ->inputInvalidClass('invalid')
            ->inputValidClass('valid')
            ->addInputClass('form-input')
            ->addLabelClass('form-label')
            ->errorClass('form-error')
            ->hintClass('form-hint')
            ->afterInput(Html::span())
            ->template("{label}\n{input}\n{error}\n{hint}")
            ->tabindex($tabIndex++)
        ?>
    <?= Html::closeTag('div') ?>
<?= $fieldFactory->fieldset()->end(); ?>
<div class="form-buttons">
    <?= $fieldFactory->submitButton()
        ->containerClass('form-button')
        ->buttonClass($rbamParameters->getButtons('submit')['attributes']['class'])
        ->buttonId('submit-button')
        ->tabindex($tabIndex++)
        ->content($translator->translate(id: $rbamParameters->getButtons('submit')['content'], category: 'rbam'))
    ?>
    <?= $fieldFactory->button()
        ->containerClass('form-button')
        ->buttonAttributes([
            'onClick' => sprintf('window.location.href = "%s"', $urlGenerator->generate('rbam.rbam'))
        ])
        ->buttonClass($rbamParameters->getButtons('cancel')['attributes']['class'])
        ->tabindex($tabIndex)
        ->content($translator->translate(id: $rbamParameters->getButtons('cancel')['content'], category: 'rbam'))
    ?>
</div>
<?= Html::form()
    ->close()
?>