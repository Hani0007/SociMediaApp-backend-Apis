<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
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
     *             @OA\Property(property="description", type="string", example="This is my first post")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Post created successfully")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $post = Post::create([
            'user_id'     => Auth::id(),
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Post added Successfully', 'post' => $post], 201);
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
        $post = Post::with(['user', 'comments', 'likes', 'media'])->findOrFail($id);
        return response()->json($post, 200);
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
        $request->validate([
            'description' => 'required|string',
        ]);

        $post = Post::where('user_id', Auth::id())->findOrFail($id);
        $post->update(['description' => $request->description]);

        return response()->json(['message' => 'Updated Successfully', 'post' => $post], 200);
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
    try {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Post already deleted or not found'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
