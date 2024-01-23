<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogCommentRequest;
use App\Http\Requests\UpdateBlogCommentRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;

class BlogCommentController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(BlogComment::class, ['blogComment', 'blogPost']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created BlogComment in storage.
     */
    public function store(StoreBlogCommentRequest $request, BlogPost $blogPost): BlogPostResource
    {
        $blogPost->comments()->create([
            'comment' => $request->comment,
            'user_id' => $request->user()->id,
        ]);

        return new BlogPostResource($blogPost);
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogComment $blogComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogComment $blogComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogCommentRequest $request, BlogComment $blogComment)
    {
        //
    }

    /**
     * Soft delete a BlogComment
     */
    public function destroy(BlogPost $blogPost, BlogComment $blogComment): JsonResponse
    {
        $blogComment->delete();

        return response()->json(null, 204);
    }

    /**
     * Restore a BlogComment
     */
    public function restore(BlogPost $blogPost, BlogComment $blogComment): BlogPostResource
    {
        $this->authorize('restore', [$blogComment, $blogPost]);

        $blogComment->restore();

        return new BlogPostResource($blogPost);
    }

    /**
     * Force delete a BlogComment
     */
    public function forceDelete(BlogPost $blogPost, BlogComment $blogComment): JsonResponse
    {
        $this->authorize('forceDelete', [$blogComment, $blogPost]);

        $blogComment->forceDelete();

        return response()->json(null, 204);
    }
}
