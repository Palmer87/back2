<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'image_url' => fake()->imageUrl(),
            'video_url' => fake()->url(),
            'typePart' => fake()->randomElement(['communique', 'discours', 'interview', 'autre']),
            'auteur' => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory(),
            'publier' => fake()->boolean(),
            'publier_le' => fake()->date(),
            'retirer_le' => fake()->date(),
        ];
    }
}
