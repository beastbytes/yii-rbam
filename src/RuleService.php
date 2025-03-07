<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Rbam\Form\RuleForm;

use Safe\Exceptions\FilesystemException;
use Yiisoft\Files\FileHelper;
use const DIRECTORY_SEPARATOR;

class RuleService implements RuleServiceInterface
{
    private const RULE_NAMESPACE = 'BeastBytes\\Yii\\Rbam\\Rule';

    private array $rules = [];

    public function __construct(private readonly string $rulesDir)
    {
        FileHelper::ensureDirectory($this->rulesDir);
        $this->rules = [];

        /** @var string $ruleFile */
        foreach (array_slice(scandir($this->rulesDir), 2) as $ruleFile) {
            $ruleClass = self::RULE_NAMESPACE . '\\' . substr($ruleFile, 0, -4);

            /** @var RbamRuleInterface $rule */
            $rule = new $ruleClass();
            $this->rules[$rule->getName()] = $rule;
        }
    }

    /**
     * @throws FilesystemException
     */
    public function delete(string $name): void
    {
        \Safe\unlink($this->rulesDir . DIRECTORY_SEPARATOR . $name . 'Rule.php');
    }

    /** @psalm-suppress MoreSpecificReturnType */
    public function getRule(string $name): ?RbamRuleInterface
    {
        return $this->rules[$name] ?? null;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getRuleNames(): array
    {
        return array_keys($this->rules);
    }

    public function save(string $name, string $description, string $code): bool
    {
        $namespace = self::RULE_NAMESPACE;
        $rule =
            <<<RULE
<?php

declare(strict_types=1);

namespace $namespace;

use BeastBytes\Yii\Rbam\RbamRuleInterface;
use BeastBytes\Yii\Rbam\RbamRuleTrait;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;
use Yiisoft\Rbac\RuleInterface;

final class {$name}Rule implements RbamRuleInterface, RuleInterface
{
    use RbamRuleTrait;
    
    private const DESCRIPTION = '$description';
    
    public function execute(?string \$userId, Item \$item, RuleContext \$context): bool
    {
    $code
    }
}
RULE;

        $fp = fopen($this->rulesDir . DIRECTORY_SEPARATOR . $name . 'Rule.php', 'wb');

        if ($fp === false) {
            return false;
        }

        $ret = (bool) fwrite($fp, $rule);
        return $ret && fclose($fp);
    }
}