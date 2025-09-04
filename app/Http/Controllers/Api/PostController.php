<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;


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
        $request->validate([
            'description' => 'required|string',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'integer|exists:media,id',
        ]);

        $post = Post::create([
            'user_id'     => Auth::id(),
            'description' => $request->description,
        ]);

        // Attach media if provided
        if ($request->filled('media_ids')) {
            $post->media()->attach($request->media_ids);
        }

        return response()->json([
            'message' => 'Post added Successfully',
            'post' => $post->load('media') // eager load attached media
        ], 201);
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
    // Fetch all posts with user and media (eager loading)
    $posts = Post::with(['user', 'media'])
                 ->orderBy('created_at', 'desc')
                 ->get();

    return response()->json($posts, 200);
}

}
