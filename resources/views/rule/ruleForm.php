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

if ($formModel->getName() === ''):
    $this->setTitle(
        $translator->translate("label.create-rule")
    );
else:
    $this->setTitle(
        $translator->translate("label.update-rule")
    );
endif;

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rules'),
        'url' => $urlGenerator->generate('rbam.ruleIndex')
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
$tabIndex = 1;
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>
<?= Html::form()
    ->post($urlGenerator->generateFromCurrent([]))
    ->csrf($csrf)
    ->id('form-rule')
    ->open()
?>
<?= Field::text($formModel, 'name')
    ->autofocus()
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::text($formModel, 'description')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::textarea($formModel, 'code')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::submitButton()
     ->containerClass('d-grid gap-2 form-floating')
     ->buttonClass($rbamParameters->getButtons('submit')['attributes']['class'])
     ->buttonId('submit-button')
     ->tabindex($tabIndex)
     ->content($translator->translate($rbamParameters->getButtons('submit')['content']))
?>
<?= Html::form()
    ->close()
?>