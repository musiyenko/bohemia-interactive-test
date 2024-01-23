<?php

namespace Tests\Feature\Console\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_accepts_only_valid_input(): void
    {
        $this->artisan('app:expire-blog 0')
            ->expectsOutput('The hours field must be at least 1.')
            ->assertFailed();

        $this->artisan('app:expire-blog 1.5')
            ->expectsOutput('The hours field must be an integer.')
            ->assertFailed();

        $this->artisan('app:expire-blog 1')
            ->assertSuccessful();
    }

    public function test_command_soft_deletes_blog_posts_and_comments(): void
    {
        $this->artisan('app:expire-blog');
        $this->assertNotSoftDeleted('blog_posts');
        $this->assertNotSoftDeleted('blog_comments');

        $this->travel(3)->hours();
        $this->artisan('app:expire-blog');
        $this->assertSoftDeleted('blog_posts');
        $this->assertSoftDeleted('blog_comments');

        $this->travelBack();
        $this->assertSoftDeleted('blog_posts');
        $this->assertSoftDeleted('blog_comments');
    }
}
