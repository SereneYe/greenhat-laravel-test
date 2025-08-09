<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Base\Traits\CamelCasing;
use Modules\User\Models\User;

class FilamentMediaLibrary extends Model
{
    use CamelCasing;

    protected $table = 'filament_media_library';

    protected $fillable = [
        'uploaded_by_user_id',
        'caption',
        'alt_text',
    ];

    /**
     * Get the user that uploaded the media.
     */
    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
