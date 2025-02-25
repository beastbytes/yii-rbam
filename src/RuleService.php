<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Rbam\Form\RuleForm;

use Yiisoft\Files\FileHelper;
use const DIRECTORY_SEPARATOR;

class RuleService implements RuleServiceInterface
{
    private const RULE_NAMESPACE = 'BeastBytes\\Yii\\Rbam\\Rule';

    private array $rules = [];

    public function __construct(private readonly string $rulesDir)
    {
        FileHelper::ensureDirectory($this->rulesDir);
        $this->load();
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    private function load(): void
    {
        $this->rules = [];

        /** @var string $ruleFile */
        foreach (array_slice(scandir($this->rulesDir), 2) as $ruleFile) {
            $ruleClass = self::RULE_NAMESPACE . '\\' . substr($ruleFile, 0, -4);

            /** @var RbamRuleInterface $rule */
            $rule = new $ruleClass();
            $this->rules[$rule->getName()] = $rule;
        }
    }

    public function getRuleNames(): array
    {
        return array_keys($this->rules);
    }

    /** @psalm-suppress MoreSpecificReturnType */
    public function getRule(string $name): ?RbamRuleInterface
    {
        return $this->rules[$name] ?? null;
    }

    public function save(RuleForm $model, ?string $previousName = null): bool
    {
        $namespace = self::RULE_NAMESPACE;
        $rule =
            <<<RULE
<?php

declare(strict_types=1);

namespace $namespace;

use BeastBytes\Yii\Rbam\RbamRuleInterface;
use BeastBytes\Yii\Rbam\RbamRuleTrait;
use ReflectionClass;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;

use const DIRECTORY_SEPARATOR;

final class {$model->getName()}Rule implements RbamRuleInterface, RuleInterface
{
    use RbamRuleTrait;
    
    private const string DESCRIPTION = '{$model->getDescription()}';
    
    public function execute(?string \$userId, Item \$item, RuleContext \$context): bool
    {
        {$model->getCode()}
    }
}
RULE;

        if (is_string($previousName) && $model->getName() !== $previousName) {
            unlink($this->rulesDir . DIRECTORY_SEPARATOR . $previousName . 'Rule.php');
        }

        $fp = fopen($this->rulesDir . DIRECTORY_SEPARATOR . $model->getName() . 'Rule.php', 'wb');

        if ($fp === false) {
            return false;
        }

        $ret = (bool) fwrite($fp, $rule);
        return $ret && fclose($fp);
    }
}