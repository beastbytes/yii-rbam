<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Item;

use ReflectionException;
use ReflectionProperty;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Translator\UnwritableCategorySourceException;

final class TranslationService implements TranslationServiceInterface
{

    public function __construct(
        private readonly string $translationsDir,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function getTranslations(string $name, string $type): array
    {
        return match ($type) {
            self::TYPE_ITEM => $this->getItemTranslations($name),
            self::TYPE_RULE => $this->getRuleTranslations($name),
        };
    }

    private function getItemTranslations(string $name): array
    {
        $translations = [];

        foreach ($this->getLocales() as $locale) {
            $translations[$locale]['description'] = $this
                ->translator
                ->translate(id: $name, category: 'rbac-item-description', locale: $locale);
            ;
        }

        return $translations;
    }

    public function getRuleTranslations(string $name): array
    {
        $translations = [];

        foreach ($this->getLocales() as $locale) {
            $translations[$locale]['description'] = $this
                ->translator
                ->translate(id: $name, category: 'rbac-rule')
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