<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    const USER = 'user';

    const MODERATOR = 'moderator';

    const ADMIN = 'admin';
}
