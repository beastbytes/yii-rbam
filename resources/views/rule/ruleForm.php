<?php

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var RuleForm $formModel
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\Rule\RuleForm;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle(
    ($formModel->isCreate()
        ? $translator->translate(id: 'header.rule.create', category: 'rbam')
        : $translator->translate(id: 'header.rule.update', category: 'rbam')
    )
);

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: 'label.rules', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rule.index')
    ],
    $this->getTitle()
];

if (!$formModel->isCreate()) {
    array_splice($breadcrumbs, 2, 0, [[
        'label' => $translator->translate('label.rule.name', ['name' => $formModel->getName()], 'rbam'),
        'url' => $urlGenerator->generate('rbam.rule.view', ['name' => $formModel->getName()]),
    ]]);
}

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
<?= Field::errorSummary($formModel) ?>
<?= Field::text($formModel, 'name')
    ->autofocus($formModel->isCreate())
    ->disabled($formModel->isUpdate())
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::text($formModel, 'description')
    ->autofocus($formModel->isUpdate())
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::textarea($formModel, 'code')
    ->beforeInput('<div>public function execute(?string $userId, Permission $item, RuleContext $context): bool {</div>')
    ->afterInput('<div>}</div>')
    ->containerClass('form-control-container')
    ->inputContainerTag('div')
    ->inputContainerClass('form-input-container code')
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
            ->content($translator->translate(
                id: $rbamParameters->getButtons('submit')['content'],
                category: 'rbam'
            ))
        ?>
        <?= Field::button()
            ->containerClass('form-button')
            ->buttonAttributes(['onClick' => sprintf(
                'window.location.href = "%s"',
                $urlGenerator->generate('rbam.rule.index')
            )])
            ->buttonClass($rbamParameters->getButtons('cancel')['attributes']['class'])
            ->tabindex($tabIndex)
            ->content($translator->translate(
                id: $rbamParameters->getButtons('cancel')['content'],
                category: 'rbam'
            ))
        ?>
    </div>
<?= Html::form()
    ->close()
?>