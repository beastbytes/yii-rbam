<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var ItemForm $formModel
 * @var array $ruleNames
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 * @var string $type
 */

use BeastBytes\Yii\Rbam\Form\ItemForm;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Csrf;

if ($formModel->getName() === ''):
    $this->setTitle(
        $translator->translate("label.add_$type")
    );
else:
    $this->setTitle(
        $translator->translate("label.update_$type")
    );
endif;

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.' . $type . 's'),
        'url' => $urlGenerator->generate('rbam.itemIndex', ['type' => $type . 's']),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);

$tabIndex = 1;
?>

<div class="card shadow mx-auto col-md-4">
    <h1 class="card-header fw-normal h3 text-center">
        <?= Html::encode($this->getTitle()) ?>
    </h1>
    <div class="card-body mt-2">
        <?= Html::form()
            ->post($urlGenerator->generateFromCurrent([]))
            ->csrf($csrf)
            ->id('form-item')
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
        <?= Field::select($formModel, 'ruleName', [
            'prompt()' => [$translator->translate('prompt.select_rule')],
            'optionsData()' => [
                array_combine($ruleNames, $ruleNames),
            ],
        ])
            ->invalidClass('invalid')
            ->validClass('valid')
            ->tabindex($tabIndex++)
        ?>
        <?= Field::submitButton()
             ->containerClass('d-grid gap-2 form-floating')
             ->buttonClass('btn btn-primary btn-lg mt-3')
             ->buttonId('submit-button')
             ->tabindex($tabIndex)
             ->content($translator->translate('label.submit'))
        ?>
        <?= Html::form()
            ->close()
        ?>
    </div>
</div>
