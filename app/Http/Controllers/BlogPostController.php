<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogPostController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(BlogPost::class, 'blogPost', [
            'except' => ['index', 'show'],
        ]);
    }

    /**
     * Display a listing of the BlogPosts
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $blogPosts = BlogPost::withCount('comments')->orderBy('comments_count', 'desc')->paginate(10);

        return BlogPostResource::collection($blogPosts);
    }

    /**
     * Store a newly created BlogPost in storage
     */
    public function store(StoreBlogPostRequest $request): BlogPostResource
    {
        $validated = $request->validated();

        $blogPost = new BlogPost([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'description' => $validated['description'],
            'slug' => $validated['slug'],
        ]);

        $blogPost->author()->associate($request->user());

        $blogPost->save();

        return new BlogPostResource($blogPost);
    }

    /**
     * Display a specified BlogPost
     */
    public function show(BlogPost $blogPost): BlogPostResource
    {
        return new BlogPostResource($blogPost);
    }

    /**
     * Update a blog post
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): BlogPostResource
    {
        $validated = $request->validated();

        $blogPost->title = $validated['title'];
        $blogPost->date = $validated['date'];
        $blogPost->description = $validated['description'];
        $blogPost->slug = $validated['slug'];

        $blogPost->save();

        return new BlogPostResource($blogPost);
    }

    /**
     * Soft delete a BlogPost
     */
    public function destroy(BlogPost $blogPost): JsonResponse
    {
        $blogPost->delete();

        return response()->json(null, 204);
    }

    /**
     * Restore a BlogPost
     */
    public function restore(BlogPost $blogPost): BlogPostResource
    {
        $this->authorize('restore', $blogPost);

        $blogPost->restore();

        return new BlogPostResource($blogPost);
    }

    /**
     * Force delete a BlogPost
     */
    public function forceDelete(BlogPost $blogPost): JsonResponse
    {
        $this->authorize('forceDelete', $blogPost);

        $blogPost->forceDelete();

        return response()->json(null, 204);
    }
}
