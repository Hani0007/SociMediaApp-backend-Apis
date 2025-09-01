<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Media",
 *     type="object",
 *     title="Media",
 *     description="Media model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="media_type", type="string", example="image"),
 *     @OA\Property(property="url", type="string", example="https://example.com/image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-01T12:00:00Z")
 * )
 */

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['media_type', 'url'];

    // Media <-> Posts many-to-many via post_media
    public function posts() {
        return $this->belongsToMany(Post::class, 'post_media','media_id','post_id')
            ->withTimestamps();
    }

    

    
}
