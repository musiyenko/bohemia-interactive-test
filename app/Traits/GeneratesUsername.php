<?php

namespace App\Traits;

use App\Models\User;

trait GeneratesUsername
{
    /**
     * Generate a username from the name and surname.
     */
    public function generateUsername(string $name, string $surname): string
    {
        $username = strtolower($surname.substr($name, 0, 3));

        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $username.$i;
            $i++;
        }

        return $username;
    }
}
