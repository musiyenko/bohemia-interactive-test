<?php

namespace Tests\Feature\Api;

use App\Enums\UserRoleEnum;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_post_comments()
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $blogPost = BlogPost::all()->random();

        $response = $this->actingAs($admin)->postJson("/api/blog/{$blogPost->slug}/comments", [
            'comment' => 'This is a comment',
        ]);

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
                        'comment_id'
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('blog_comments', [
            'comment' => 'This is a comment',
            'user_id' => $admin->id,
            'blog_post_id' => $blogPost->id,
        ]);
    }

    public function test_moderators_can_post_comments()
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        $blogPost = BlogPost::all()->random();

        $response = $this->actingAs($moderator)->postJson("/api/blog/{$blogPost->slug}/comments", [
            'comment' => 'This is a comment',
        ]);

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
                        'comment_id'
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('blog_comments', [
            'comment' => 'This is a comment',
            'user_id' => $moderator->id,
            'blog_post_id' => $blogPost->id,
        ]);
    }

    public function test_regular_users_can_post_comments()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $blogPost = BlogPost::all()->random();

        $response = $this->actingAs($user)->postJson("/api/blog/{$blogPost->slug}/comments", [
            'comment' => 'This is a comment',
        ]);

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
                        'comment_id'
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('blog_comments', [
            'comment' => 'This is a comment',
            'user_id' => $user->id,
            'blog_post_id' => $blogPost->id,
        ]);
    }

    public function test_guests_can_not_post_comments()
    {
        $blogPost = BlogPost::all()->random();

        $response = $this->postJson("/api/blog/{$blogPost->slug}/comments", [
            'comment' => 'This is a comment',
        ]);

        $response->assertUnauthorized();
    }

    public function test_admins_can_delete_comments()
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $response = $this->actingAs($admin)->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_moderators_can_not_delete_comments()
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $comment = $user->blogComments->random();

        $blogPost = $comment->blogPost;

        $response = $this->actingAs($moderator)->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}");

        $response->assertForbidden();

        $this->assertNotSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_regular_users_can_delete_their_own_comments()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $comment = $user->blogComments->random();
        $blogPost = $comment->blogPost;

        $response = $this->actingAs($user)->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_regular_users_can_not_delete_other_users_comments()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $user2 = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->where('id', '!=', $user->id)->first();

        $comment = $user2->blogComments->random();
        $blogPost = $comment->blogPost;

        $response = $this->actingAs($user)->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}");

        $response->assertForbidden();

        $this->assertNotSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_guests_can_not_delete_comments()
    {
        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $response = $this->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}");

        $response->assertUnauthorized();

        $this->assertNotSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_admins_can_restore_comments()
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $comment->delete();

        $response = $this->actingAs($admin)->postJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}/restore");

        $response->assertOk();

        $this->assertNotSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_moderators_can_not_restore_comments()
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $comment->delete();

        $response = $this->actingAs($moderator)->postJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}/restore");

        $response->assertForbidden();

        $this->assertSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_regular_users_can_not_restore_comments()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $comment->delete();

        $response = $this->actingAs($user)->postJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}/restore");

        $response->assertForbidden();

        $this->assertSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_guests_can_not_restore_comments()
    {
        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $comment->delete();

        $response = $this->postJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}/restore");

        $response->assertUnauthorized();

        $this->assertSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_admins_can_force_delete_comments()
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::ADMIN);
        })->first();

        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $comment->delete();

        $response = $this->actingAs($admin)->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}/force-delete");

        $response->assertNoContent();

        $this->assertDatabaseMissing('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_moderators_can_not_force_delete_comments()
    {
        $moderator = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::MODERATOR);
        })->first();

        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $comment->delete();

        $response = $this->actingAs($moderator)->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}/force-delete");

        $response->assertForbidden();

        $this->assertSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_regular_users_can_not_force_delete_comments()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', UserRoleEnum::USER);
        })->first();

        $blogPost = BlogPost::all()->random();

        $comment = $blogPost->comments->random();

        $comment->delete();

        $response = $this->actingAs($user)->deleteJson("/api/blog/{$blogPost->slug}/comments/{$comment->id}/force-delete");

        $response->assertForbidden();

        $this->assertSoftDeleted('blog_comments', [
            'id' => $comment->id,
        ]);
    }
}
