<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;

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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blogPosts = BlogPost::withCount('comments')->orderBy('comments_count', 'desc')->paginate(10);

        if ($request->expectsJson()) {
            return BlogPostResource::collection($blogPosts);
        }

        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogPostRequest $request)
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
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {
        return new BlogPostResource($blogPost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost)
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
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return response()->json(null, 204);
    }

    /**
     * Restore a soft deleted BlogPost.
     *
     * @param  string  $slug
     * @return void
     */
    public function restore(BlogPost $blogPost)
    {
        $this->authorize('restore', $blogPost);

        $blogPost->restore();

        return new BlogPostResource($blogPost);
    }

    /**
     * Force delete a BlogPost.
     *
     * @param  string  $slug
     * @return void
     */
    public function forceDelete(BlogPost $blogPost)
    {
        $this->authorize('forceDelete', $blogPost);

        $blogPost->forceDelete();

        return response()->json(null, 204);
    }
}
