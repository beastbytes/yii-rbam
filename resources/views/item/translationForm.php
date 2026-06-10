<?php

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var FieldFactory $fieldFactory
 * @var TranslationForm $formModel
 * @var Item $item
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var array $translations
 * @var TranslatorInterface $translator
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\Item\TranslationForm;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\FormModel\FieldFactory;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncode;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle($translator->translate(sprintf('header.%s.translate', $type)));

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: 'label.' . $type . 's', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.item.index', ['type' => $type . 's']),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);

$tabIndex = 1;
?>

<h2 class="header"><?= $this->getTitle() ?></h2>
<h3><?= $translator->translate(sprintf('label.%s.name', $type), ['name' => $item->getName()]) ?></h3>

<?= Html::form()
    ->attributes(['x-data' => '{active: null}'])
    ->post($urlGenerator->generateFromCurrent([]))
    ->csrf($csrf)
    ->id('form-item')
    ->open()
?>

<?php foreach ($formModel->getTranslations() as $locale => $translation): ?>
    <?= $fieldFactory->fieldset()
            ->containerAttributes([
                'x-data' => sprintf('{
                    id: "%s",
                    get expanded() {
                        return this.active === this.id
                    },
                    set expanded(value) {
                        this.active = value ? this.id : null
                    },
                }', $locale),
                'role' => 'region'
            ])
        ->inputContainerAttributes(['x-show' => 'expanded', 'x-collapse' => true])
        ->legend(
            NoEncode::string(sprintf(
                '<span>%s</span><span x-show="expanded" x-cloak>&ndash;</span><span x-show="!expanded" x-cloak>+</span>',
                $translation->getLocale()
            )),
            [
                'class' => 'flex justify-between',
                '@click' => 'expanded = !expanded',
                ':aria-expanded' => 'expanded'
            ]
        )
        ->content(
            Html::div()
            ->attributes([
                'x-show' => 'expanded',
                'x-collapse' => true,
            ])
            ->content(
                $fieldFactory->hidden($translation, 'locale')
                    ->inputId(sprintf('translation-%s-locale', $locale))
                    ->name(sprintf('TranslationForm[translations][%s][locale]', $locale))
                ,
                $fieldFactory->text($translation, 'description')
                    ->inputId(sprintf('translation-%s-description', $locale))
                    ->name(sprintf('TranslationForm[translations][%s][description]', $locale))
                    ->required(true)
                    ->containerClass('form-control-container')
                    ->inputContainerTag('div')
                    ->inputContainerClass('form-input-container')
                    ->addInputClass('form-input')
                    ->addLabelClass('form-label')
                    ->afterInput(Html::span())
                    ->tabindex($tabIndex++)
                ,
            )
        )
    ?>
 <?php endforeach; ?>

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
            'onClick' => sprintf(
                'window.location.href = "%s"',
                $urlGenerator->generate('rbam.item.view', ['name' => $item->getName(), 'type' => $type])
            )
        ])
        ->buttonClass($rbamParameters->getButtons('cancel')['attributes']['class'])
        ->tabindex($tabIndex)
        ->content($translator->translate(id: $rbamParameters->getButtons('cancel')['content'], category: 'rbam'))
    ?>
</div>

<?= Html::form()
    ->close()
?>


