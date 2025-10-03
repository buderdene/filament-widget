<?php

use Illuminate\Support\Str;
use Buderdene\FilamentWidget\Models\Author;
use Buderdene\FilamentWidget\Models\Post;
use Buderdene\FilamentWidget\Resources\PostResource;

use function Pest\Livewire\livewire;

it('can list', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});

it('can create', function () {
    $newData = Post::factory()->make();

    $this->assertDatabaseHas(Author::class, [
        'name' => $newData->author->name,
        'email' => $newData->author->email,
    ]);

    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm([
            'title' => $newData->title,
            'excerpt' => $newData->excerpt,
            'content' => $newData->content,
            'widget_author_id' => $newData->author->getKey(),
            'widget_category_id' => $newData->category->getKey(),
        ])
        ->assertFormSet([
            'slug' => Str::slug($newData->title),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Post::class, [
        'title' => $newData->title,
        'slug' => $newData->slug,
        'excerpt' => $newData->excerpt,
        'content' => $newData->content,
        'widget_author_id' => $newData->author->getKey(),
        'widget_category_id' => $newData->category->getKey(),
    ]);
});

it('can retrieve data', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getRouteKey(),
    ])
        ->assertFormSet([
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
        ]);
});
