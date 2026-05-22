<?php

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Diagram\HierarchyDiagramInterface;
use BeastBytes\Yii\Rbam\Diagram\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\Rule\RuleService;
use BeastBytes\Yii\Rbam\Rule\RuleServiceInterface;
use Yiisoft\Aliases\Aliases;

/** @var array $params */

return [
    HierarchyDiagramInterface::class => MermaidHierarchyDiagram::class,
    RbamParameters::class => [
        'class' => RbamParameters::class,
        '__construct()' => [
            'parameters' => $params['beastbytes/yii-rbam']
        ],
    ],
    RuleServiceInterface::class => static fn (Aliases $aliases) => new RuleService(
        $aliases->get($params['yiisoft/aliases']['aliases']['@rbacRules'])
    ),
];