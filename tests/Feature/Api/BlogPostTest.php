<?php

namespace Tests\Feature\Api;

use App\Enums\UserRoleEnum;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_posts_can_be_listed_by_everyone(): void
    {
        BlogPost::factory()->count(3)->create();

        $response = $this->getJson('/api/blog');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'author',
                    'date',
                    'slug',
                    'total_comments',
                    'comments' => [
                        '*' => [
                            'author',
                            'date',
                            'comment',
                            'comment_id',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertOk();
    }

    public function test_singular_blog_post_can_be_retrieved_by_everyone(): void
    {
        BlogPost::factory()->create();

        $blogPost = BlogPost::first();

        $response = $this->getJson("/api/blog/{$blogPost->slug}");

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'date',
                'slug',
                'description',
                'total_comments',
                'comments' => [
                    '*' => [
                        'author',
                        'date',
                        'comment',
                        'comment_id',
                    ],
                ],
            ],
        ]);

        $response->assertOk();
    }

    public function test_admins_can_create_blog_posts(): void
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $response = $this->actingAs($admin)->postJson('/api/blog', [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title-admin',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'date',
                'slug',
                'description',
                'total_comments',
                'comments' => [
                    '*' => [
                        'author',
                        'date',
                        'comment',
                        'comment_id',
                    ],
                ],
            ],
        ]);

        $response->assertCreated();
    }

    public function test_moderators_can_create_blog_posts(): void
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        $response = $this->actingAs($moderator)->postJson('/api/blog', [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'date',
                'slug',
                'description',
                'total_comments',
                'comments' => [
                    '*' => [
                        'author',
                        'date',
                        'comment',
                        'comment_id',
                    ],
                ],
            ],
        ]);

        $response->assertCreated();
    }

    public function test_regular_users_can_not_create_blog_posts(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $response = $this->actingAs($user)->postJson('/api/blog', [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title-user',
        ]);

        $response->assertForbidden();
    }

    public function test_guests_can_not_create_blog_posts(): void
    {
        $this->assertGuest();

        $response = $this->postJson('/api/blog', [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title',
        ]);

        $response->assertUnauthorized();
    }

    public function test_admins_can_update_blog_posts(): void
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $blogPost = BlogPost::first();

        $response = $this->actingAs($admin)->putJson("/api/blog/{$blogPost->slug}", [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title-admin',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'date',
                'slug',
                'description',
                'total_comments',
                'comments' => [
                    '*' => [
                        'author',
                        'date',
                        'comment',
                        'comment_id',
                    ],
                ],
            ],
        ]);

        $response->assertOk();
    }

    public function test_moderators_can_update_blog_posts(): void
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        $blogPost = BlogPost::first();

        $response = $this->actingAs($moderator)->putJson("/api/blog/{$blogPost->slug}", [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'date',
                'slug',
                'description',
                'total_comments',
                'comments' => [
                    '*' => [
                        'author',
                        'date',
                        'comment',
                        'comment_id',
                    ],
                ],
            ],
        ]);

        $response->assertOk();
    }

    public function test_regular_users_can_not_update_blog_posts(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $blogPost = BlogPost::first();

        $response = $this->actingAs($user)->putJson("/api/blog/{$blogPost->slug}", [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title-user',
        ]);

        $response->assertForbidden();
    }

    public function test_guests_can_not_update_blog_posts(): void
    {
        $this->assertGuest();

        $blogPost = BlogPost::first();

        $response = $this->putJson("/api/blog/{$blogPost->slug}", [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
            'slug' => 'test-title',
        ]);

        $response->assertUnauthorized();
    }

    public function test_admins_can_update_blog_posts_without_changing_slug(): void
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $blogPost = BlogPost::first();

        $response = $this->actingAs($admin)->putJson("/api/blog/{$blogPost->slug}", [
            'title' => 'Test Title',
            'date' => '2021-01-01',
            'description' => 'Test Description',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'date',
                'slug',
                'description',
                'total_comments',
                'comments' => [
                    '*' => [
                        'author',
                        'date',
                        'comment',
                        'comment_id',
                    ],
                ],
            ],
        ]);

        $response->assertOk();
    }

    public function test_admins_can_delete_blog_posts(): void
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $blogPost = BlogPost::first();

        $response = $this->actingAs($admin)->deleteJson("/api/blog/{$blogPost->slug}");

        $response->assertNoContent();

        $this->assertSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_moderators_can_not_delete_blog_posts(): void
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        $blogPost = BlogPost::first();

        $response = $this->actingAs($moderator)->deleteJson("/api/blog/{$blogPost->slug}");

        $response->assertForbidden();

        $this->assertNotSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_regular_users_can_not_delete_blog_posts(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $blogPost = BlogPost::first();

        $response = $this->actingAs($user)->deleteJson("/api/blog/{$blogPost->slug}");

        $response->assertForbidden();

        $this->assertNotSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_guests_can_not_delete_blog_posts(): void
    {
        $this->assertGuest();

        $blogPost = BlogPost::first();

        $response = $this->deleteJson("/api/blog/{$blogPost->slug}");

        $response->assertUnauthorized();

        $this->assertNotSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_admins_can_restore_blog_posts(): void
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->actingAs($admin)->postJson("/api/blog/{$blogPost->slug}/restore");

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'title',
                'author',
                'date',
                'slug',
                'description',
                'total_comments',
                'comments' => [
                    '*' => [
                        'author',
                        'date',
                        'comment',
                        'comment_id',
                    ],
                ],
            ],
        ]);

        $this->assertNotSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_moderators_can_not_restore_blog_posts(): void
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->actingAs($moderator)->postJson("/api/blog/{$blogPost->slug}/restore");

        $response->assertForbidden();

        $this->assertSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_regular_users_can_not_restore_blog_posts(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->actingAs($user)->postJson("/api/blog/{$blogPost->slug}/restore");

        $response->assertForbidden();

        $this->assertSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_guests_can_not_restore_blog_posts(): void
    {
        $this->assertGuest();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->postJson("/api/blog/{$blogPost->slug}/restore");

        $response->assertUnauthorized();

        $this->assertSoftDeleted('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_admins_can_force_delete_blog_posts(): void
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->actingAs($admin)->deleteJson("/api/blog/{$blogPost->slug}/force-delete");

        $response->assertNoContent();

        $this->assertDatabaseMissing('blog_posts', [
            'id' => $blogPost->id,
        ]);
    }

    public function test_moderators_can_not_force_delete_blog_posts(): void
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->actingAs($moderator)->deleteJson("/api/blog/{$blogPost->slug}/force-delete");

        $response->assertForbidden();
    }

    public function test_regular_users_can_not_force_delete_blog_posts(): void
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->actingAs($user)->deleteJson("/api/blog/{$blogPost->slug}/force-delete");

        $response->assertForbidden();
    }

    public function test_guests_can_not_force_delete_blog_posts(): void
    {
        $this->assertGuest();

        BlogPost::first()->delete();

        $blogPost = BlogPost::onlyTrashed()->first();

        $response = $this->deleteJson("/api/blog/{$blogPost->slug}/force-delete");

        $response->assertUnauthorized();
    }
}
