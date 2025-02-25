<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var ItemForm $formModel
 * @var RbamParameters $rbamParameters
 * @var array $ruleNames
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 * @var string $type
 */

use BeastBytes\Yii\Rbam\Form\ItemForm;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle(
    ($formModel->getName() === ''
        ? $translator->translate("label.create-$type")
        : $translator->translate("label.update-$type")
    )
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.' . $type . 's'),
        'url' => $urlGenerator->generate('rbam.itemIndex', ['type' => $type . 's']),
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
    ->autofocus()
    ->containerClass('form-control-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::text($formModel, 'description')
    ->containerClass('form-control-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::select($formModel, 'ruleName', [
    'prompt()' => [$translator->translate('prompt.select-rule')],
    'optionsData()' => [
        array_combine($ruleNames, $ruleNames),
    ],
])
    ->containerClass('form-control-container')
    ->addContainerClass(empty($ruleNames) ? 'disabled' : '')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->disabled(empty($ruleNames))
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::submitButton()
     ->containerClass('form-buttons')
     ->buttonClass($rbamParameters->getButtons('submit')['attributes']['class'])
     ->buttonId('submit-button')
     ->tabindex($tabIndex)
     ->content($translator->translate($rbamParameters->getButtons('submit')['content']))
?>
<?= Html::form()
    ->close()
?>