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

$this->setTitle($translator->translate('header.rbac.initialise'));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
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
<?= Field::errorSummary($formModel) ?>
<?= Field::text($formModel, 'userId')
    ->autofocus(true)
    ->required(true)
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->afterInput(Html::span())
    ->tabindex($tabIndex++)
?>
<div class="form-buttons">
    <?= Field::submitButton()
        ->containerClass('form-button')
        ->buttonClass($rbamParameters->getButtons('submit')['attributes']['class'])
        ->buttonId('submit-button')
        ->tabindex($tabIndex++)
        ->content($translator->translate($rbamParameters->getButtons('submit')['content']))
    ?>
    <?= Field::button()
        ->containerClass('form-button')
        ->buttonAttributes([
            'onClick' => sprintf('window.location.href = "%s"', $urlGenerator->generate('rbam.rbam'))
        ])
        ->buttonClass($rbamParameters->getButtons('cancel')['attributes']['class'])
        ->tabindex($tabIndex)
        ->content($translator->translate($rbamParameters->getButtons('cancel')['content']))
    ?>
</div>
<?= Html::form()
    ->close()
?>