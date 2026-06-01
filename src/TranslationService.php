<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Rbam\Rule\RuleInterface;
use ReflectionException;
use ReflectionProperty;
use Yiisoft\Rbac\Item;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Translator\UnwritableCategorySourceException;

final readonly class TranslationService implements TranslationServiceInterface
{

    public function __construct(
        private string $translationsDir,
        private TranslatorInterface $translator,
    )
    {
    }

    public function deleteItem(Item $item): void
    {
        $categorySource = $this->getCategorySource('rbac-item');

        foreach ($this->getLocales() as $locale) {
            $messages = $categorySource->getMessages($locale);
            if (array_key_exists($item->getName(), $messages)) {
                unset($messages[$item->getName()]);
            }
            if (array_key_exists($item->getDescription(), $messages)) {
                unset($messages[$item->getDescription()]);
            }
            $categorySource->write($locale, $messages);
        }
    }

    public function deleteRule(string $name): void
    {
        $categorySource = $this->getCategorySource('rbac-rule');

        foreach ($this->getLocales() as $locale) {
            $messages = $categorySource->getMessages($locale);
            if (array_key_exists($name, $messages)) {
                unset($messages[$name]);
            }
            $categorySource->write($locale, $messages);
        }
    }

    public function getItemTranslations(Item $item): array
    {
        $translations = [];

        foreach ($this->getLocales() as $locale) {
            $translations[$locale]['name'] = $this
                ->translator
                ->translate(id: $item->getName(), category: 'rbac-item', locale: $locale);
            ;
            $translations[$locale]['description'] = $this
                ->translator
                ->translate(id: $item->getDescription(), category: 'rbac-item', locale: $locale);
            ;
        }

        return $translations;
    }

    public function getRuleTranslations(RuleInterface $rule): array
    {
        $translations = [];

        foreach ($this->getLocales() as $locale) {
            $translations[$locale]['description'] = $this
                ->translator
                ->translate(id: $rule->getDescription(), category: 'rbac-rule', locale: $locale)
            ;
        }

        return $translations;
    }

    /**
     * @param array{string: array{string: array{string: string}}} $translations
     * @throws UnwritableCategorySourceException
     * @throws ReflectionException
     */
    public function save(array $translations): void
    {
        foreach ($translations as $locale => $categories) {
            foreach ($categories as $category => $localeMessages) {
                $categorySource = $this->getCategorySource($category);
                $messages = $categorySource->getMessages($locale);

                foreach ($localeMessages as $id => $message) {
                    $messages[$id] = compact('message');
                }

                ksort($messages);
                $categorySource->write($locale, $messages);
            }
        }
    }

    public function updateItem(Item $oldItem, Item $newItem): void
    {
        $categorySource = $this->getCategorySource('rbac-item');

        foreach ($this->getLocales() as $locale) {
            $messages = $categorySource->getMessages($locale);

            foreach (['Description', 'Name'] as $method) {
                $getter = "get$method";

                $old = $oldItem->$getter();
                $new = $newItem->$getter();

                if ($old !== $new) {
                    $messages[$new] = $messages[$old];
                    unset($messages[$old]);
                }
            }

            ksort($messages);
            $categorySource->write($locale, $messages);
        }
    }

    public function updateRule(string $oldDescription, string $newDescription): void
    {
        $categorySource = $this->getCategorySource('rbac-rule');

        foreach ($this->getLocales() as $locale) {
            $messages = $categorySource->getMessages($locale);

            if ($oldDescription !== $newDescription) {
                $messages[$newDescription] = $messages[$oldDescription];
                unset($messages[$oldDescription]);
            }

            ksort($messages);
            $categorySource->write($locale, $messages);
        }
    }

    /**
     * @throws ReflectionException
     */
    private function getCategorySource(string $category): CategorySource
    {
        $categorySources = (new ReflectionProperty($this->translator, 'categorySources'))
            ->getValue($this->translator)
        ;

        return $categorySources[$category][0];
    }

    private function getLocales(): array
    {
        return array_slice(scandir($this->translationsDir), 2);
    }
}