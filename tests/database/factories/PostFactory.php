<?php

namespace Buderdene\FilamentWidget\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Buderdene\FilamentWidget\Models\Author;
use Buderdene\FilamentWidget\Models\Category;
use Buderdene\FilamentWidget\Models\Post;

class PostFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $title = $this->faker->unique()->sentence(4),
            'slug' => Str::slug($title),
            'content' => $this->faker->realText(),
            'excerpt' => $this->faker->realTextBetween(50, 1000),
            'published_at' => $this->faker->dateTimeBetween('-6 month', '+1 month'),
            'widget_author_id' => Author::factory()->create(),
            'widget_category_id' => Category::factory()->create(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
