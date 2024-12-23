<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetailsVideo extends Model
{
    use HasFactory;

    protected $table = 'extended_details_video';

    protected $fillable = ['title', 'data', 'extended_details_videos_id'];

    protected $hidden = ['created_at', 'updated_at', 'extended_details_id', 'extended_details_videos_id', 'id'];


    //belongs to extra data videos
    public function video()
    {
        return $this->belongsTo(ExtendedDetailsVideos::class, 'extended_details_videos_id');
    }

}
