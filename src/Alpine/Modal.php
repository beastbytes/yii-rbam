<?php

namespace BeastBytes\Yii\Rbam\Alpine;

use Yiisoft\Html\Tag\Button;
use Yiisoft\Widget\Widget;

/**
 * Usage:
 *
 * Modal is a `content capture` widget; the content captured being the trigger for the Modal
 *
 * Initialise and set up the widget as required, then surround the trigger text with `begin()` and `end()` calls
 */
final class Modal extends Widget
{
    /**
     * @var list<Button> $buttons
     */
    private array $buttons = [];
    private string $containerClass = 'modal';
    private string $content = '';
    private string $contentClass = 'content';
    private ?string $description = null;
    private string $footerClass = 'footer';
    private string $overlayClass = 'overlay';
    private string $panelClass = 'panel';
    private ?string $title = null;
    private string $titleClass = 'title';
    private Button $trigger;

    /**
     * Buttons for the modal
     *
     * @param Button ...$button Buttons
     * @return self A new instance with buttons
     */
    public function buttons(Button ...$button): self
    {
        $new = clone $this;
        $new->buttons = $button;
        return $new;
    }

    /**
     * @param string $containerClass The dialog container class
     * @return self A new instance with the dialog container class
     */
    public function containerClass(string $containerClass): self
    {
        $new = clone $this;
        $new->containerClass = $containerClass;
        return $new;
    }

    /**
     * @param string $content The dialog content
     * @return self A new instance with the dialog content
     */
    public function content(string $content): self
    {
        $new = clone $this;
        $new->content = $content;
        return $new;
    }

    /**
     * Set the dialog description
     *
     * If this method is not called, no description is generated
     *
     * @param string $description The dialog description
     * @return self A new instance with the dialog description
     */
    public function description(string $description): self
    {
        $new = clone $this;
        $new->description = $description;
        return $new;
    }

    /**
     * Set overlay class
     *
     * The widget provides the positional styles; use to provide appearance
     *
     * @param string $class Class for the overlay
     * @return self A new instance with overlay classes
     */
    public function overlayClass(string $class): self
    {
        $new = clone $this;
        $new->overlayClass = $class;
        return $new;
    }

    /**
     * @param string $title The dialog title
     * @return self A new instance with the dialog title
     */
    public function title(string $title): self
    {
        $new = clone $this;
        $new->title = $title;
        return $new;
    }

    public function titleClass(string $class): self
    {
        $new = clone $this;
        $new->titleClass = $class;
        return $new;
    }

    public function trigger(Button $trigger): self
    {
        $new = clone $this;
        $new->trigger = $trigger;
        return $new;
    }

    public function triggerClass(string $triggerClass): self
    {
        $new = clone $this;
        $new->triggerClass = $triggerClass;
        return $new;
    }

    public function render(): string
    {
        return sprintf(
            '<div x-data="{open:false}" class="%s">%s%s</div>',
            $this->containerClass,
            $this->renderTrigger(),
            $this->renderDialog()
        );
    }

    private function renderDialog(): string
    {
        return sprintf(
            '<div x-dialog x-model="open">%s</div>',
            $this->renderOverlay()
        );
    }

    private function renderOverlay(): string
    {
        return sprintf(
            '<div style="position:fixed;display:flex;inset:0;min-height:100vh;align-items:center;justify-content:center;" class="%s">%s</div>',
            $this->overlayClass,
            $this->renderPanel()
        );
    }

    private function renderPanel(): string
    {
        return sprintf(
            '<div x-dialog:panel class="%s">%s%s%s%s</div>',
            $this->panelClass,
            $this->renderTitle(),
            $this->renderDescription(),
            $this->renderContent(),
            $this->renderFooter()
        );
    }

    private function renderContent(): string
    {
        return sprintf(
            '<div class="%s">%s</div>',
            $this->contentClass,
            $this->content
        );
    }

    private function renderDescription(): string
    {
        return is_string($this->description)
            ? sprintf(
                '<div x-dialog:description style="display:none;">%s</div>',
                $this->description
            )
            : ''
        ;
    }

    private function renderTitle(): string
    {
        return is_string($this->title)
            ? sprintf(
                '<div x-dialog:title class="%s">%s</div>',
                $this->titleClass,
                $this->title
            )
            : ''
        ;
    }

    private function renderTrigger(): string
    {
        return $this->trigger
            ->addAttributes([
                'x-on:click' => 'open=true'
            ])
            ->render()
        ;
    }

    private function renderFooter(): string
    {
        return sprintf(
            '<div class="%s">%s</div>',
            $this->footerClass,
            $this->renderButtons()
        );
    }

    private function renderButtons(): string
    {
        $buttons = [];

        foreach ($this->buttons as $button) {
            $buttons[] = $button->addAttributes([
                'x-on:click' => 'open=false',
            ])
                ->render()
            ;
        }

        return implode('', $buttons);
    }

    /*


    <div x-data="{open: false, detail: {}}" class="modal" id="modal" @modal.window="detail = $event.detail; open=true;">
        <div x-dialog x-model="open" x-cloak class="dialog">
            <div x-dialog:overlay x-transition.opacity class="overlay">
                <div x-dialog:panel x-transition class="panel">
                    <button type="button" @click="$dialog.close()" class="close-button">
                        <span x-text="detail.closeDialog" class="sr-only"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" stroke="white" fill="currentColor" aria-hidden="true">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"></path>
                        </svg>
                    </button>
                    <div x-dialog:title x-html="detail.title" class="title"></div>
                    <div x-html="detail.content" class="content"></div>
                    <div class="footer">
                        <button type="button" @click="rbam.action($data.detail.buttons.continue)" class="btn btn_continue">
                            <?= $translator->translate('button.continue') ?>
                        </button>
                        <button type="button" @click="$dialog.close()" class="btn btn_cancel">
                            <?= $translator->translate('button.cancel') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    */






}