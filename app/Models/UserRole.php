<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'role',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check whether the user is a regular user.
     */
    public function isUser(): bool
    {
        return $this->role === UserRoleEnum::USER;
    }

    /**
     * Check whether the user is a moderator.
     */
    public function isModerator(): bool
    {
        return $this->role === UserRoleEnum::MODERATOR;
    }

    /**
     * Check whether the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRoleEnum::ADMIN;
    }
}
