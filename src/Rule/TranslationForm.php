<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Collection;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

class TranslationForm extends FormModel
{
    #[Collection(Translation::class)]
    #[Each([new Nested(Translation::class)])]
    private array $translations = [];

    public function __construct()
    {
    }

    public function withTranslations(array $translations): self
    {
        $new = clone $this;

        foreach ($translations as $locale => $translation) {
            $new->translations[$locale] = new Translation(
                $translation['description'],
                $locale
            );
        }

        return $new;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function hasTranslations(): bool
    {
        return count($this->translations) > 0;
    }
}