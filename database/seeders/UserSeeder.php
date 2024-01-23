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

        $team = User::take(3)->get();

        $team->get(0)->role()->update([
            'role' => UserRoleEnum::ADMIN,
        ]);

        $team->get(0)->update([
            'email' => 'admin@dayz.com',
        ]);

        $team->get(1)->role()->update([
            'role' => UserRoleEnum::MODERATOR,
        ]);

        $team->get(1)->update([
            'email' => 'mod@dayz.com',
        ]);

        $team->get(2)->update([
            'email' => 'user@dayz.com',
        ]);
    }
}
