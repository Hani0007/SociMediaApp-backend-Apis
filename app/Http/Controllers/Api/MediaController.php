<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MediaService;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Media",
 *     description="Media management for posts"
 * )
 */
class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @OA\Get(
     *     path="/api/media",
     *     tags={"Media"},
     *     summary="Get all media",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of all media")
     * )
     */
    public function index()
    {
        return response()->json($this->mediaService->getAllMedia(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/media",
     *     tags={"Media"},
     *     summary="Upload new media and optionally attach to a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"media_type","file"},
     *                 @OA\Property(property="media_type", type="string", example="image"),
     *                 @OA\Property(property="file", type="string", format="binary", description="The image file to upload"),
     *                 @OA\Property(property="post_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Media uploaded successfully"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media_type' => 'required|string',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'post_id' => 'nullable|integer|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $media = $this->mediaService->uploadMedia($request->file('file'), $request->media_type);

        if ($request->filled('post_id')) {
            $this->mediaService->attachMediaToPost($media->id, $request->post_id);
        }

        return response()->json($media->load('posts'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/media/{id}",
     *     tags={"Media"},
     *     summary="Get media by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Media ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Media details"),
     *     @OA\Response(response=404, description="Media not found")
     * )
     */
    public function show($id)
    {
        $media = $this->mediaService->getMedia($id);
        return response()->json($media, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{postId}/media/{mediaId}",
     *     tags={"Media"},
     *     summary="Delete media attached to a post",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="Post ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="mediaId",
     *         in="path",
     *         required=true,
     *         description="Media ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Media deleted successfully"),
     *     @OA\Response(response=404, description="Media not found for this post")
     * )
     */
    public function destroy($postId, $mediaId)
    {
        $this->mediaService->deleteMedia($mediaId, $postId);

        return response()->json([
            'message' => 'Media deleted successfully'
        ], 200);
    }
}
