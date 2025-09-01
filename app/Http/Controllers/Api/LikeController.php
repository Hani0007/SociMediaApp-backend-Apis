<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Likes",
 *     description="Like management"
 * )
 */
class LikeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/like",
     *     tags={"Likes"},
     *     summary="Like a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="post_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Liked successfully")
     * )
     */
    public function store(Request $request)
    {
        $request->validate(['post_id' => 'required|exists:posts,id']);

        $like = Like::firstOrCreate([
            'user_id' => Auth::id(),
            'post_id' => $request->post_id,
        ]);

        return response()->json(['message' => 'Liked Successfully', 'like' => $like], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/like/{id}",
     *     tags={"Likes"},
     *     summary="Unlike a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Unlike successful")
     * )
     */
  public function destroy($id)
{
    try {
        $like = Like::where('user_id', Auth::id())->findOrFail($id);
        $like->delete();

        return response()->json([
            'message' => 'Unlike successfully'
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Like already removed or not found'
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
     *     path="/api/posts/{id}/likes",
     *     tags={"Likes"},
     *     summary="Get likes of a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of likes")
     * )
     */
    public function index($id)
    {
        $likes = Like::with('user')->where('post_id', $id)->get();
        return response()->json($likes, 200);
    }
}
