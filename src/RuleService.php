<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Rbam\Form\RuleForm;

use const DIRECTORY_SEPARATOR;

class RuleService implements RuleServiceInterface
{
    private const RULE_NAMESPACE = 'BeastBytes\\Yii\\Rbam\\Rule';

    private array $rules = [];

    public function __construct(private readonly string $rulesDir)
    {
        if (
            !file_exists($this->rulesDir)
            && !mkdir($this->rulesDir, 0744, true)
            && !is_dir($this->rulesDir)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->rulesDir));
        }

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

            /** @var RuleInterface $rule */
            $rule = new $ruleClass();
            $this->rules[$rule->getName()] = $rule;
        }
    }

    public function getRuleNames(): array
    {
        return array_keys($this->rules);
    }

    /** @psalm-suppress MoreSpecificReturnType */
    public function getRule(string $name): ?RuleInterface
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

use BeastBytes\Yii\Rbam\RuleInterface;
use ReflectionClass;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;

use const DIRECTORY_SEPARATOR;

final class {$model->getName()}Rule implements RuleInterface
{
    private string \$description = '{$model->getDescription()}';
    
    public function execute(?string \$userId, Item \$item, RuleContext \$ruleContext): bool
    {
        {$model->getCode()}
    }
    
    public function getCode(): string
    {
        \$reflectionClass = new ReflectionClass(\$this);
        \$executeMethod = \$reflectionClass->getMethod('execute');

        \$offset = \$executeMethod->getStartLine() + 1;
        \$length = \$executeMethod->getEndLine() - 1 - \$offset;

        \$filename = __DIR__ . DIRECTORY_SEPARATOR . \$reflectionClass->getShortName() . '.php';
        \$lines = array_slice(file(\$filename), \$offset, \$length);

        foreach (\$lines as &\$line) {
            \$line = trim(\$line);
        }

        return implode("\\n", \$lines);
    }
    
    public function getDescription(): string
    {
        return \$this->description;
    }
    
    public function getName(): string
    {
        \$reflectionClass = new ReflectionClass(\$this);
        return substr(\$reflectionClass->getShortName(), 0, -4);
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
