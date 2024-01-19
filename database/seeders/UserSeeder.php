<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This is a better approach as the one below ( count(50000) ) batches all the 50000 users in one query,
        // resulting in duplicate generated usernames, as the username verification is based on existing records.
        for ($i = 0; $i < 100; $i++) {
            User::factory()->create();
        }

        /* User::factory()
            ->count(50000)
            ->create(); */

        $team = User::take(2)->get();

        $team->first()->role()->update([
            'role' => UserRoleEnum::ADMIN,
        ]);

        $team->last()->role()->update([
            'role' => UserRoleEnum::MODERATOR,
        ]);
    }
}
