<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Comments",
 *     description="Comment management"
 * )
 */
class CommentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/comments",
     *     tags={"Comments"},
     *     summary="Add a comment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"post_id","comment_text"},
     *             @OA\Property(property="post_id", type="integer"),
     *             @OA\Property(property="comment_text", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Comment added successfully")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_id'      => 'required|exists:posts,id',
            'comment_text' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id'      => Auth::id(),
            'post_id'      => $request->post_id,
            'comment_text' => $request->comment_text,
        ]);

        return response()->json(['message' => 'Comment Added', 'comment' => $comment], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/comment/{id}",
     *     tags={"Comments"},
     *     summary="Update a comment",
     *    security={{"bearerAuth":{}}}, 
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(@OA\Property(property="comment_text", type="string"))
     *     ),
     *     @OA\Response(response=200, description="Comment updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate(['comment_text' => 'required|string']);

        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->update(['comment_text' => $request->comment_text]);

        return response()->json(['message' => 'Comment Updated', 'comment' => $comment], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/comment/{id}",
     *     tags={"Comments"},
     *     summary="Delete a comment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Comment deleted successfully")
     * )
     */
public function destroy($id)
{
    try {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Comment not found or already deleted'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}
}

