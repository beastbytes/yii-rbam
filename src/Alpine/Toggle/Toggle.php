<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Alpine\Toggle;

use Yiisoft\Assets\AssetManager;
use Yiisoft\Form\Field\Base\InputField;
use Yiisoft\Form\Field\Base\ValidationClass\ValidationClassTrait;
use Yiisoft\Html\Html;

/**
 * A toggle input element that represents a state or option that can be toggled.
 */
final class Toggle extends InputField
{
    use ValidationClassTrait;

    private const string ACTIVE_CLASS = 'active';
    private const string CONTAINER_CLASS = 'alpine-toggle';
    private const string INACTIVE_CLASS = 'inactive';
    private const string SLIDER_CLASS = 'slider';

    protected string $template = "{input}\n{hint}";

    private array $inputLabelAttributes = [];
    private ?string $inputValue = '1';
    private ?string $uncheckValue = '0';

    public function __construct(AssetManager $assetManager)
    {
        $assetManager->register(ToggleAsset::class);
    }

    /**
     * Identifies the element (or elements) that describes the object.
     *
     * @link https://w3c.github.io/aria/#aria-describedby
     */
    public function ariaDescribedBy(?string ...$value): self
    {
        $new = clone $this;
        $new->inputAttributes['aria-describedby'] = array_filter($value, static fn(?string $v): bool => $v !== null);
        return $new;
    }

    /**
     * Defines a string value that labels the current element.
     *
     * @link https://w3c.github.io/aria/#aria-label
     */
    public function ariaLabel(?string $value): self
    {
        $new = clone $this;
        $new->inputAttributes['aria-label'] = $value;
        return $new;
    }

    /**
     * Focus on the control (put cursor into it) when the page loads. Only one form element could be in focus
     * at the same time.
     *
     * @link https://html.spec.whatwg.org/multipage/interaction.html#attr-fe-autofocus
     */
    public function autofocus(bool $value = true): self
    {
        $new = clone $this;
        $new->inputAttributes['autofocus'] = $value;
        return $new;
    }

    public function inputValue(bool|float|int|string|Stringable|null $value): self
    {
        $new = clone $this;
        $new->inputValue = $this->prepareToggleValue($value);
        return $new;
    }

    public function inputLabelAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->inputLabelAttributes = $attributes;
        return $new;
    }

    public function addInputLabelAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->inputLabelAttributes = array_merge($new->inputLabelAttributes, $attributes);
        return $new;
    }

    /**
     * The `tabindex` attribute indicates that its element can be focused, and where it participates in sequential
     * keyboard navigation (usually with the Tab key, hence the name).
     *
     * It accepts an integer as a value, with different results depending on the integer's value:
     *
     * - A negative value (usually `tabindex="-1"`) means that the element is not reachable via sequential keyboard
     *   navigation, but could be focused with Javascript or visually. It's mostly useful to create accessible widgets
     *   with JavaScript.
     * - `tabindex="0"` means that the element should be focusable in sequential keyboard navigation, but its order is
     *   defined by the document's source order.
     * - A positive value means the element should be focusable in sequential keyboard navigation, with its order
     *   defined by the value of the number. That is, `tabindex="4"` is focused before `tabindex="5"`, but after
     *   `tabindex="3"`.
     *
     * @link https://html.spec.whatwg.org/multipage/interaction.html#attr-tabindex
     */
    public function tabIndex(?int $value): self
    {
        $new = clone $this;
        $new->inputAttributes['tabindex'] = $value;
        return $new;
    }

    /**
     * @param bool|float|int|string|Stringable|null $value Value that corresponds to "unchecked" state of the input.
     */
    public function uncheckValue(bool|float|int|string|Stringable|null $value): self
    {
        $new = clone $this;
        $new->uncheckValue = $this->prepareToggleValue($value);
        return $new;
    }

    protected function generateInput(): string
    {
        $this->containerAttributes = array_merge(
            $this->containerAttributes,
            [
                'x-data' => true,
                'x-switch:group' => true,
            ]
        );

        $inputAttributes = array_merge(
            $this->inputAttributes,
            [
                'x-switch' => true,
                'name' => $this->getName(),
                'value' => $this->inputValue,
                ':class' => '$switch.isChecked ? \'checked\' : \'unchecked\'',
            ],
            $this->getValue() === $this->inputValue ? ['default-checked' => true] : []
        );

        $inputLabelAttributes = array_merge($this->inputLabelAttributes, ['x-switch:label' => true]);

        Html::addCssClass($this->containerAttributes, self::CONTAINER_CLASS);
        Html::addCssClass($inputAttributes, self::SLIDER_CLASS);

        return Html::hiddenInput(
            $this->getName(),
            attributes: [
                'x-switch' => true,
                'value' => $this->uncheckValue
            ]
        )
        . Html::button(
            Html::span(attributes: [
                'aria-hidden' => true,
                'class' => 'button',
                ':class' => '$switch.isChecked ? \'checked\' : \'unchecked\'',
            ]),
            $inputAttributes
        )
        . Html::label($this->getInputData()->getLabel())->attributes($inputLabelAttributes);
    }

    protected function prepareContainerAttributes(array &$attributes): void
    {
        $this->addValidationClassToAttributes(
            $attributes,
            $this->getInputData(),
            $this->hasCustomError() ? true : null,
        );
    }

    private function prepareToggleValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }
}