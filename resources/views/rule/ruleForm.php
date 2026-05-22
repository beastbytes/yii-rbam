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
        ? $translator->translate('label.rule.create')
        : $translator->translate('label.rule.update')
    )
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rules'),
        'url' => $urlGenerator->generate('rbam.rule.index')
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
<?= Field::errorSummary($formModel) ?>
<?= Field::text($formModel, 'name')
    ->autofocus($formModel->isCreate())
    ->disabled($formModel->isUpdate())
    ->containerClass('form-control-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::text($formModel, 'description')
    ->autofocus($formModel->isUpdate())
    ->containerClass('form-control-container')
    ->addInputClass('form-input')
    ->addLabelClass('form-label')
    ->invalidClass('invalid')
    ->validClass('valid')
    ->tabindex($tabIndex++)
?>
<?= Field::textarea($formModel, 'code')
    ->beforeInput("public function execute(?string \$userId, Permission \$item, RuleContext \$context): bool\n{")
    ->afterInput('}')
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
            ->buttonAttributes(['onClick' => sprintf('window.location.href = "%s"', $urlGenerator->generate('rbam.rule.index'))])
            ->buttonClass($rbamParameters->getButtons('cancel')['attributes']['class'])
            ->tabindex($tabIndex)
            ->content($translator->translate($rbamParameters->getButtons('cancel')['content']))
        ?>
    </div>
<?= Html::form()
    ->close()
?>