<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'First name',
            'surname' => 'Last name',
            'nickname' => 'User123',
            'phone' => '12356789',
            'address' => 'Kamyshevo, 9',
            'city' => 'Kamyshevo',
            'state' => 'Chernarus',
            'zip' => '123456',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_usernames_should_be_unique(): void
    {
        $this->post('/register', [
            'name' => 'firstname',
            'surname' => 'lastname',
            'nickname' => 'User123',
            'phone' => '12356789',
            'address' => 'Kamyshevo, 9',
            'city' => 'Kamyshevo',
            'state' => 'Chernarus',
            'zip' => '123456',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->post('/logout');

        $this->post('/register', [
            'name' => 'firstname',
            'surname' => 'lastname',
            'nickname' => 'User123456',
            'phone' => '12356789',
            'address' => 'Kamyshevo, 9',
            'city' => 'Kamyshevo',
            'state' => 'Chernarus',
            'zip' => '123456',
            'email' => 'test2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $username1 = User::where('email', 'test@example.com')->first()->username;
        $username2 = User::where('email', 'test2@example.com')->first()->username;

        $this->assertNotEquals($username1, $username2);
        $this->assertEquals($username1.'1', $username2);
    }
}
