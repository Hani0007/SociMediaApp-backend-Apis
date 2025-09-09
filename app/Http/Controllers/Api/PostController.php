<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PostService;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     required={"id", "user_id", "description"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="description", type="string", example="This is my first post"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @OA\Get(
     *     path="/api/allposts",
     *     tags={"Posts"},
     *     summary="Get all posts",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Posts retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $posts = $this->postService->getAllPosts();
        return response()->json($posts, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Get a specific post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post retrieved successfully")
     * )
     */
    public function show($id)
    {
        $post = $this->postService->getPost($id);
        return response()->json($post, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description"},
     *             @OA\Property(property="description", type="string", example="This is my first post"),
     *             @OA\Property(
     *                 property="media_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post added Successfully"),
     *             @OA\Property(property="post", ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'integer|exists:media,id',
        ]);

        $post = $this->postService->createPost($data);

        return response()->json([
            'message' => 'Post added Successfully',
            'post' => $post
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Update a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Post updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'description' => 'required|string',
        ]);

        $post = $this->postService->updatePost($id, $data);

        return response()->json([
            'message' => 'Updated Successfully',
            'post' => $post
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Delete a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $this->postService->deletePost($id);

        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
