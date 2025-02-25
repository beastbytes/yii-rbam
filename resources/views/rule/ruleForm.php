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
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
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
        ? $translator->translate('label.create-rule')
        : $translator->translate('label.update-rule')
    )
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rules'),
        'url' => $urlGenerator->generate('rbam.ruleIndex')
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
$tabIndex = 1;
?>

<h2 class='header'><?= $this->getTitle() ?></h2>

<?= Html::form()
    ->post($urlGenerator->generateFromCurrent([]))
    ->csrf($csrf)
    ->id('form-rule')
    ->open()
?>
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
<?= Field::textarea($formModel, 'code')
    ->containerClass('form-control-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
    <div class='form-buttons'>
        <?= Field::submitButton()
            ->containerClass('form-button')
            ->buttonClass($rbamParameters->getButtons('submit')['attributes']['class'])
            ->buttonId('submit-button')
            ->tabindex($tabIndex++)
            ->content($translator->translate($rbamParameters->getButtons('submit')['content']))
        ?>
        <?= Field::button()
            ->containerClass('form-button')
            ->buttonAttributes(['onClick' => 'history.back()'])
            ->buttonClass($rbamParameters->getButtons('cancel')['attributes']['class'])
            ->tabindex($tabIndex)
            ->content($translator->translate($rbamParameters->getButtons('cancel')['content']))
        ?>
    </div>
<?= Html::form()
    ->close()
?>