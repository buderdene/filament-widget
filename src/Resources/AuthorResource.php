<?php

namespace Buderdene\FilamentWidget\Resources;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Buderdene\FilamentWidget\Models\Author;
use Buderdene\FilamentWidget\Resources\AuthorResource\Pages;
use Buderdene\FilamentWidget\Traits\HasContentEditor;
use UnitEnum;

class AuthorResource extends Resource
{
    use HasContentEditor;

    protected static ?string $model = Author::class;

    protected static ?string $slug = 'widget/authors';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | null | UnitEnum $navigationGroup = 'widget';

    protected static string | null | BackedEnum $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-widget::filament-widget.name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(__('filament-widget::filament-widget.email'))
                            ->required()
                            ->email()
                            ->unique(Author::class, 'email', fn ($record) => $record),
                        FileUpload::make('photo')
                            ->label(__('filament-widget::filament-widget.photo'))
                            ->image()
                            ->disk(config('filament-widget.avatar.disk', 'public'))
                            ->visibility(config('filament-widget.avatar.visibility', 'public'))
                            ->maxSize(config('filament-widget.avatar.maxSize', 5120))
                            ->directory(config('filament-widget.avatar.directory', 'widget'))
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        self::getContentEditor('bio'),
                        TextInput::make('github_handle')
                            ->label(__('filament-widget::filament-widget.github')),
                        TextInput::make('twitter_handle')
                            ->label(__('filament-widget::filament-widget.twitter')),
                    ])
                    ->columns([
                        'sm' => 2,
                    ])
                    ->columnSpan(2),
                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->default('—')
                            ->label(__('filament-widget::filament-widget.created_at'))
                            ->state(fn (?Author $record) => $record?->created_at?->diffForHumans()),
                        TextEntry::make('updated_at')
                            ->default('—')
                            ->label(__('filament-widget::filament-widget.last_modified_at'))
                            ->state(fn (?Author $record) => $record?->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->disk(config('filament-widget.avatar.disk', 'public'))
                    ->visibility(config('filament-widget.banner.visibility', 'public'))
                    ->label(__('filament-widget::filament-widget.photo'))
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-widget::filament-widget.name'))
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament-widget::filament-widget.email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('github_handle')
                    ->label(__('filament-widget::filament-widget.github')),
                Tables\Columns\TextColumn::make('twitter_handle')
                    ->label(__('filament-widget::filament-widget.twitter')),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-widget::filament-widget.authors');
    }

    public static function getModelLabel(): string
    {
        return __('filament-widget::filament-widget.author');
    }
}
