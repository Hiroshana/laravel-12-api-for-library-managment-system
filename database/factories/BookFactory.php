<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->paragraph(),
            'author_id' => Author::inRandomOrder()->first()?->id ?? Author::factory(),
            'genre' => $this->faker->randomElement(['Fiction', 'Non-Fiction', 'Science Fiction', 'Fantasy', 'Biography', 'History', 'Mystery']),
            'published_at' => $this->faker->date(),
            'total_copies' => $this->faker->numberBetween(1, 50),
            'available_copies' => $this->faker->numberBetween(0, 50),
            'cover_image' => $this->faker->imageUrl(200, 300, 'books', true),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
