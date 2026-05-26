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

$this->setTitle($translator->translate(id: 'header.rbac.clear', category: 'rbam'));

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

<div class="danger">
    <?= $translator->translate(id: 'message.rbac.clear', category: 'rbam') ?>
</div>

<?= Html::form()
    ->post($urlGenerator->generate('rbam.clear'))
    ->csrf($csrf)
    ->id('form-item')
    ->open()
?>
<?= Field::errorSummary($formModel) ?>
<?= Field::text($formModel, 'code')
    ->required(true)
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->afterInput(Html::span())
    ->hintAttributes(['inert' => true])
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