<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds, uses a small amount of entries to run tests faster
     */
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->create();

        $team = User::take(2)->get();

        $team->first()->role()->update([
            'role' => UserRoleEnum::ADMIN,
        ]);

        $team->last()->role()->update([
            'role' => UserRoleEnum::MODERATOR,
        ]);

        BlogPost::factory()
            ->count(100)
            ->create();
    }
}
