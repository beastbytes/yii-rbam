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
<?= Field::errorSummary($formModel)
    ->containerClass('form-error')
?>
<?= Field::text($formModel, 'userId')
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
<?= Field::fieldset()
    ->containerAttributes([
        'x-data' => '{initialiseApplication: false}',
        'x-init' => "initialiseApplication = document.getElementById('initialiseform-initialiseapplication').checked",
    ])
    ->containerClass('border-t mt-1 pt-1')
    ->content(
        Html::div(NoEncode::string($translator->translate(id: 'message.initialise', category: 'rbam'))),
        Field::checkbox($formModel, 'initialiseApplication')
            ->uncheckValue(false)
            ->containerClass('form-control-container')
            ->inputContainerTag('div')
            ->inputContainerClass('form-input-container')
            ->inputAttributes(['x-model' => 'initialiseApplication'])
            ->addInputClass('form-input')
            ->addLabelClass('form-label')
            ->errorClass('form-error')
            ->hintClass('form-hint')
            ->tabindex($tabIndex++)
        ,
        Html::div()
            ->attributes([
                'x-show' => 'initialiseApplication',
                'x-transition' => true,
            ])
            ->content(
                Html::div(NoEncode::string($translator->translate(
                    id: 'message.initialise-application',
                    category: 'rbam'
                ))),
                Field::text($formModel, 'srcDir')
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
                ,
                Field::text($formModel, 'except')
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
                ,
                Field::text($formModel, 'only')
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
                ,
            )
    )
    ->legend($translator->translate(id: 'label.application', category: 'rbam'))
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