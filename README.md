![](https://raw.githubusercontent.com/Buderdene/filament-widget/main/art/banner.jpg)

# Filament widget Builder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/Buderdene/filament-widget.svg)](https://packagist.org/packages/Buderdene/filament-widget)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/Buderdene/filament-widget/run-tests.yml?branch=main&label=tests)](https://github.com/Buderdene/filament-widget/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/Buderdene/filament-widget/fix-php-code-style-issues.yml?branch-main&label=code%20style)](https://github.com/Buderdene/filament-widget/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/Buderdene/filament-widget.svg?style=flat-square)](https://packagist.org/packages/Buderdene/filament-widget)

A faceless widget content manager with configurable richtext and markdown support for a filament admin panel.

![](https://raw.githubusercontent.com/Buderdene/filament-widget/main/art/screen1.png)

## Filament Admin Panel

This package is tailored for [Filament Admin Panel](https://filamentphp.com/).

Make sure you have installed the admin panel before you continue with the installation. You can check the [documentation here](https://filamentphp.com/docs/admin)

## Supported Versions

PHP: `8.1` & `8.2`

Laravel: `10`

## Installation

You can install the package via composer:

```bash
composer require Buderdene/filament-widget

php artisan filament-widget:install

php artisan storage:link

php artisan migrate
```

You'll have to register the plugin in your panel provider.

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ...
        ->plugin(
            Buderdene\FilamentWidget\widgetPlugin::make()
        );
}
```

### Authorization/Access Control
You can authorize the plugin for users with a specific role/permission:

```php
\Buderdene\FilamentWidget\widgetPlugin::make()
    ->authorizePost(fn() => auth()->user()->can('edit.posts'));
    ->authorizeAuthor(fn() => auth()->user()->can('edit.authors'));
    ->authorizeCategory(fn() => auth()->user()->can('edit.category'));
 ```

## Displaying your content

Filament widget builder is faceless, it doesn't have any opinions on how you display your content in your frontend. You can use the widget models in your controllers to display the different resources:

-   `Buderdene\FilamentWidget\Models\Post`
-   `Buderdene\FilamentWidget\Models\Author`
-   `Buderdene\FilamentWidget\Models\Category`

### Posts & Drafts

```php
$posts = Post::published()->get();

$drafts = Post::draft()->get();

```

### Post Content

```php
$post = Post::find($id);

$post->id;
$post->title;
$post->slug;
$post->excerpt;
$post->banner_url;
$post->content;
$post->published_at;
```

### Post Category & Author

```php
$post = Post::with(['author', 'category'])->find($id);

$author = $post->author;

$author->id;
$author->name;
$author->email;
$author->photo;
$author->bio;
$author->github_handle;
$author->twitter_handle;


$category = $post->category;

$category->id;
$category->name;
$category->slug;
$category->description;
$category->is_visible;
$category->seo_title;
$category->seo_description;

```

### Configurations

This is the contents of the published config file:

```php
<?php

return [

    /**
     * Supported content editors: richtext & markdown:
     *      \Filament\Forms\Components\RichEditor::class
     *      \Filament\Forms\Components\MarkdownEditor::class
     */
    'editor'  => \Filament\Forms\Components\RichEditor::class,

    /**
     * Buttons for text editor toolbar.
     */
    'toolbar_buttons' => [
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'undo',
    ],

    /**
     * Configs for Posts that give you the option to change
     * the sort column and direction of the Posts.
     */
    'sort' => [
        'column' => 'published_at',
        'direction' => 'asc',
    ],
];
```

## More Screenshots

![](https://raw.githubusercontent.com/Buderdene/filament-widget/main/art/screen2.png)

---

![](https://raw.githubusercontent.com/Buderdene/filament-widget/main/art/screen3.png)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Buderdene](https://github.com/Buderdene)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
