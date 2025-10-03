<?php

namespace Buderdene\FilamentWidget\Resources;

use BackedEnum;
use Filament\Forms;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Buderdene\FilamentWidget\Models\Category;
use Buderdene\FilamentWidget\Models\Post;
use Buderdene\FilamentWidget\Resources\PostResource\Pages;
use Buderdene\FilamentWidget\Traits\HasContentEditor;
use UnitEnum;

use function now;

class PostResource extends Resource
{
    use HasContentEditor;

    protected static ?string $model = Post::class;

    protected static ?string $slug = 'widget/posts';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | null | UnitEnum $navigationGroup = 'widget';

    protected static string | null | BackedEnum $navigationIcon = 'heroicon-o-newspaper';

    protected static ?int $navigationSort = 0;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner')
                    ->disk(config('filament-widget.banner.disk', 'public'))
                    ->visibility(config('filament-widget.banner.visibility', 'public'))
                    ->label(__('filament-widget::filament-widget.banner'))
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament-widget::filament-widget.title'))
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('filament-widget::filament-widget.author_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament-widget::filament-widget.category_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('filament-widget::filament-widget.published_at'))
                    ->date()
                    ->sortable(),
            ])->defaultSort(
                config('filament-widget.sort.column', 'published_at'),
                config('filament-widget.sort.direction', 'asc')
            )
            ->filters([
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('published_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament-widget::filament-widget.title'))
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
                            ->unique(Post::class, 'slug', fn ($record) => $record),

                        Forms\Components\Textarea::make('excerpt')
                            ->label(__('filament-widget::filament-widget.excerpt'))
                            ->rows(2)
                            ->minLength(50)
                            ->maxLength(1000)
                            ->columnSpan([
                                'sm' => 2,
                            ]),

                        Forms\Components\FileUpload::make('banner')
                            ->label(__('filament-widget::filament-widget.banner'))
                            ->image()
                            ->maxSize(config('filament-widget.banner.maxSize', 5120))
                            ->imageCropAspectRatio(config('filament-widget.banner.cropAspectRatio', '16:9'))
                            ->disk(config('filament-widget.banner.disk', 'public'))
                            ->visibility(config('filament-widget.banner.visibility', 'public'))
                            ->directory(config('filament-widget.banner.directory', 'widget'))
                            ->columnSpan([
                                'sm' => 2,
                            ]),

                        self::getContentEditor('content'),

                        Forms\Components\Select::make('widget_author_id')
                            ->label(__('filament-widget::filament-widget.author'))
                            ->relationship(name: 'author', titleAttribute: 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email(),
                            ])
                            ->preload()
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('widget_category_id')
                            ->label(__('filament-widget::filament-widget.category'))
                            ->relationship(name: 'category', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
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
                            ]),

                        Forms\Components\DatePicker::make('published_at')
                            ->label(__('filament-widget::filament-widget.published_date')),
                        SpatieTagsInput::make('tags')
                            ->label(__('filament-widget::filament-widget.tags')),
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
                            ->state(fn (?Post $record) => $record?->created_at?->diffForHumans()),
                        TextEntry::make('updated_at')
                            ->default('—')
                            ->label(__('filament-widget::filament-widget.last_modified_at'))
                            ->state(fn (?Post $record) => $record?->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['author', 'category']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'author.name', 'category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Post $record */
        $details = [];

        if ($record->author) {
            $details['Author'] = $record->author->name;
        }

        if ($record->category) {
            $details['Category'] = $record->category->name;
        }

        return $details;
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-widget::filament-widget.posts');
    }

    public static function getModelLabel(): string
    {
        return __('filament-widget::filament-widget.post');
    }
}
