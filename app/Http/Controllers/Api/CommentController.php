<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommentService;

/**
 * @OA\Tag(
 *     name="Comments",
 *     description="Comment management for posts"
 * )
 */
class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Add a comment to a post
     *
     * @OA\Post(
     *     path="/api/posts/{id}/comments",
     *     tags={"Comments"},
     *     summary="Add a comment to a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Post ID to comment on",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment_text"},
     *             @OA\Property(property="comment_text", type="string", example="Nice post!")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Comment added successfully"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request, $postId)
    {
        $data = $request->validate([
            'comment_text' => 'required|string|max:255',
        ]);

        $comment = $this->commentService->addComment($postId, $data['comment_text']);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Update a comment
     *
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     tags={"Comments"},
     *     summary="Update a comment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment_text"},
     *             @OA\Property(property="comment_text", type="string", example="Updated comment text")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Comment updated successfully"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'comment_text' => 'required|string|max:255',
        ]);

        $comment = $this->commentService->updateComment($id, $data['comment_text']);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment,
        ], 200);
    }

    /**
     * Delete a comment
     *
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     tags={"Comments"},
     *     summary="Delete a comment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Comment ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Comment deleted successfully"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Comment not found")
     * )
     */
    public function destroy($id)
    {
        $this->commentService->deleteComment($id);

        return response()->json([
            'message' => 'Comment deleted successfully',
        ], 200);
    }

    /**
     * Get all comments for a post
     *
     * @OA\Get(
     *     path="/api/posts/{id}/comments",
     *     tags={"Comments"},
     *     summary="Get all comments of a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Post ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of comments with count",
     *         @OA\JsonContent(
     *             @OA\Property(property="post_id", type="integer", example=21),
     *             @OA\Property(property="comments_count", type="integer", example=5),
     *             @OA\Property(
     *                 property="comments",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="comment_text", type="string", example="Nice post!"),
     *                     @OA\Property(property="user_id", type="integer", example=3),
     *                     @OA\Property(property="post_id", type="integer", example=21),
     *                     @OA\Property(property="created_at", type="string", example="2025-09-04T10:15:30Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2025-09-04T10:15:30Z"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="Abdul Hanan")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index($postId)
    {
        $comments = $this->commentService->getCommentsByPost($postId);

        return response()->json([
            'post_id' => $postId,
            'comments_count' => count($comments),
            'comments' => $comments,
        ], 200);
    }
}
