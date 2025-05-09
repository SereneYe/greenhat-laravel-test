<?php

namespace Modules\Media\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReadPrivateMediaUrl
{
    use AsAction;

    public function handle(?Media $media): ?string
    {
        if (! $media?->exists) {
            return null;
        }

        return $media->disk === 'local'
            ? $media->getTemporaryUrl(now()->addMinutes(app()->environment('production') ? 5 : 60))
            : $media->getUrl();
    }
}
