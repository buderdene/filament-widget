<?php

namespace Buderdene\FilamentWidget\Resources;

use BackedEnum;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Buderdene\FilamentWidget\Models\Category;
use Buderdene\FilamentWidget\Resources\CategoryResource\Pages;
use Buderdene\FilamentWidget\Traits\HasContentEditor;
use UnitEnum;

class CategoryResource extends Resource
{
    use HasContentEditor;

    protected static ?string $model = Category::class;

    protected static ?string $slug = 'widget/categories';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | null | UnitEnum $navigationGroup = 'widget';

    protected static string | null | BackedEnum $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament-widget::filament-widget.name'))
                            ->required()
                            ->live(true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('filament-widget::filament-widget.slug'))
                            ->required()
                            ->unique(Category::class, 'slug', fn ($record) => $record),
                        self::getContentEditor('description'),
                        Forms\Components\Toggle::make('is_visible')
                            ->label(__('filament-widget::filament-widget.visible_to_guests'))
                            ->default(true),
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
                            ->state(fn (?Category $record) => $record?->created_at?->diffForHumans()),
                        TextEntry::make('updated_at')
                            ->default('—')
                            ->label(__('filament-widget::filament-widget.last_modified_at'))
                            ->state(fn (?Category $record) => $record?->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-widget::filament-widget.name'))
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('filament-widget::filament-widget.slug'))
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->label(__('filament-widget::filament-widget.visibility')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-widget::filament-widget.last_updated'))
                    ->date(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-widget::filament-widget.categories');
    }

    public static function getModelLabel(): string
    {
        return __('filament-widget::filament-widget.category');
    }
}
