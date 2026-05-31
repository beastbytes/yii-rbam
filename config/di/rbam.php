<?php

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Diagram\HierarchyDiagramInterface;
use BeastBytes\Yii\Rbam\Diagram\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\InitialisationService;
use BeastBytes\Yii\Rbam\InitialisationServiceInterface;
use BeastBytes\Yii\Rbam\Item\TranslationService;
use BeastBytes\Yii\Rbam\Item\TranslationServiceInterface;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\Rule\RuleService;
use BeastBytes\Yii\Rbam\Rule\RuleServiceInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Translator\TranslatorInterface;

/** @var array $params */

return [
    HierarchyDiagramInterface::class => MermaidHierarchyDiagram::class,
    InitialisationServiceInterface::class => InitialisationService::class,
    RbamParameters::class => [
        'class' => RbamParameters::class,
        '__construct()' => [
            'parameters' => array_merge($params['beastbytes/yii-rbam'], $params['yiisoft/rbac']),
        ],
    ],
    RuleServiceInterface::class => static fn (Aliases $aliases) => new RuleService(
        $aliases->get($params['yiisoft/aliases']['aliases']['@rbacRules'])
    ),
    TranslationServiceInterface::class =>
        static fn (Aliases $aliases, TranslatorInterface $translator) => new TranslationService(
            $aliases->get($params['yiisoft/aliases']['aliases']['@rbacTranslations']),
            $translator
        )
    ,
];