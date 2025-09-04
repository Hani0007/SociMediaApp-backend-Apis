<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Media",
 *     description="Media management"
 * )
 */
class MediaController extends Controller
{
    public function index()
    {
        $media = Media::all();
        return response()->json($media, 200);
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
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="The image file to upload"
     *                 ),
     *                 @OA\Property(property="post_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Media uploaded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
     *     ),
     *     @OA\Response(response=400, description="Validation error")
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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // ✅ Store file inside storage/app/public/media
        $path = $request->file('file')->store('media', 'public');
        $url = asset('storage/' . $path);

        $media = Media::create([
            'media_type' => $request->media_type,
            'url' => $url, // save public URL in DB
        ]);

        // Attach to post if post_id provided
        if ($request->filled('post_id')) {
            $media->posts()->attach($request->post_id);
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
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media details",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
     *     ),
     *     @OA\Response(response=404, description="Media not found")
     * )
     */
    public function show($id)
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json(['message' => 'Media not found'], 404);
        }

        return response()->json($media, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{postId}/media/{mediaId}",
     *     tags={"Media"},
     *     summary="Delete media by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="mediaId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Media deleted successfully"),
     *     @OA\Response(response=404, description="Media not found")
     * )
     */
    public function destroy($postId, $mediaId)
    {
        $media = Media::where('id', $mediaId)
            ->whereHas('posts', function ($q) use ($postId) {
                $q->where('posts.id', $postId);
            })
            ->first();

        if (!$media) {
            return response()->json(['message' => 'Media not found for this post'], 404);
        }

        $media->delete();

        return response()->json(['message' => 'Media deleted successfully']);
    }
}
