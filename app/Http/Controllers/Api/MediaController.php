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
    // /**
    //  * @OA\Get(
    //  *     path="/api/media",
    //  *     tags={"Media"},
    //  *     summary="Get all media",
    //  *     security={{"bearerAuth":{}}},
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="List of media",
    //  *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Media"))
    //  *     )
    //  * )
    //  */
    public function index()
    {
        $media = Media::all();
        return response()->json($media, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/media",
     *     tags={"Media"},
     *     summary="Upload new media",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"media_type","url"},
     *             @OA\Property(property="media_type", type="string", example="image"),
     *             @OA\Property(property="url", type="string", example="https://example.com/image.jpg")
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
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $media = Media::create([
            'media_type' => $request->media_type,
            'url' => $request->url,
        ]);

        return response()->json($media, 201);
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
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Media deleted successfully"),
     *     @OA\Response(response=404, description="Media not found")
     * )
     */
    public function destroy($id, $mediaId)
    {
        $media = Media::where('id', $mediaId)
            ->whereHas('posts', function ($q) use ($id) {
                $q->where('posts.id', $id);
            })
            ->first();

        if (!$media) {
            return response()->json(['message' => 'Media not found for this post'], 404);
        }

        $media->delete();

        return response()->json(['message' => 'Media deleted successfully']);
    }
}
