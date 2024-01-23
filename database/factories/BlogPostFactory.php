<?php

namespace Database\Factories;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->realTextBetween(10, 64),
            'user_id' => User::whereHas('role', function ($query) {
                $query->where('role', UserRoleEnum::MODERATOR);
            })->first()->id,
            'date' => $this->faker->date(),
            'description' => $this->faker->realTextBetween(100, 4096),
            'slug' => $this->faker->slug(),
        ];
    }
}
