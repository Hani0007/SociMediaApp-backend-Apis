<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LikeService;

/**
 * @OA\Tag(
 *     name="Likes",
 *     description="Like management"
 * )
 */
class LikeController extends Controller
{
    protected $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

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

        $like = $this->likeService->likePost($request->post_id);

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
        $this->likeService->unlikePost($id);

        return response()->json([
            'message' => 'Unlike successfully'
        ], 200);
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
        $count = $this->likeService->getLikesCount($id);

        return response()->json([
            'post_id' => $id,
            'likes_count' => $count
        ], 200);
    }
}
