<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

use Safe\Exceptions\FilesystemException;
use Throwable;
use Yiisoft\Files\FileHelper;

use const DIRECTORY_SEPARATOR;

class RuleService implements RuleServiceInterface
{
    private const string CLASS_SUFFIX = 'Rule';
    private const string FILE_EXTENSION = '.php';
    private const string RULE_NAMESPACE = 'BeastBytes\\Yii\\Rbam\\Rbac\\Rule';

    public function __construct(private readonly string $rulesDir)
    {
        FileHelper::ensureDirectory($this->rulesDir);
    }

    /**
     * @throws FilesystemException
     */
    public function delete(string $name): void
    {

        \Safe\unlink($this->rulesDir . DIRECTORY_SEPARATOR . $this->filename($name));
    }

    /** @psalm-suppress MoreSpecificReturnType */
    public function getRule(string $name): ?RuleInterface
    {
        $ruleClass = self::RULE_NAMESPACE . '\\' . $this->classname($name);

        try {
            return new $ruleClass();
        } catch (Throwable) {
            return null;
        }
    }

    public function getRules(): array
    {
        $rules = array_flip($this->getRuleClasses());
        array_walk($rules, fn(string &$rule) => $rule = new $rule());
        return $rules;
    }

    /**
     * Returns rules as a map of class => name
     * @return array<string, string>
     */
    public function getRuleClasses(): array
    {
        $rules = [];

        /** @var string $ruleFile */
        foreach (array_slice(scandir($this->rulesDir), 2) as $ruleFile) {
            $rules[self::RULE_NAMESPACE . '\\' . substr($ruleFile, 0, -4)]
                = substr($ruleFile, 0, -8)

            ;
        }

        return $rules;
    }

    public function isUnique(string $name): bool
    {
        /** @var string $ruleFile */
        foreach (array_slice(scandir($this->rulesDir), 2) as $ruleFile) {
            if (substr($ruleFile, 0, -8) === $name) {
                return false;
            }
        }

        return true;
    }

    public function save(string $name, string $description, string $code): bool
    {
        $code = str_repeat(' ', 8)
            . str_replace("\n", "\n" . str_repeat(' ', 8), $code)
        ;
        $namespace = self::RULE_NAMESPACE;
        $rule =
            <<<RULE
<?php

declare(strict_types=1);

namespace $namespace;

use BeastBytes\Yii\Rbam\Rule\RuleInterface as RbamRuleInterface;
use BeastBytes\Yii\Rbam\Rule\RuleTrait;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;
use Yiisoft\Rbac\RuleInterface;

final class {$name}Rule implements RbamRuleInterface, RuleInterface
{
    use RuleTrait;
    
    private const string DESCRIPTION = '$description';
    
    public function execute(?string \$userId, Item \$item, RuleContext \$context): bool
    {
$code
    }
}
RULE;

        $filePath = $this->rulesDir . DIRECTORY_SEPARATOR . $this->filename($name);

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($filePath, force: true);
        }

        return file_put_contents($filePath, $rule) === strlen($rule);
    }

    private function classname(string $name): string
    {
        return $name . self::CLASS_SUFFIX;
    }

    private function filename(string $name): string
    {
        return $this->classname($name) . self::FILE_EXTENSION;
    }
}