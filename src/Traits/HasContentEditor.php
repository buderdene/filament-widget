<?php

namespace Buderdene\FilamentWidget\Traits;

trait HasContentEditor
{
    public static function getContentEditor(string $field)
    {
        $defaultEditor = config('filament-widget.editor');

        return $defaultEditor::make($field)
            ->label(__('filament-widget::filament-widget.content'))
            ->required()
            ->toolbarButtons(config('filament-widget.toolbar_buttons'))
            ->columnSpan([
                'sm' => 2,
            ]);
    }
}
