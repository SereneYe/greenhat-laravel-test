<?php

namespace Modules\Media\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class DownloadPrivateMedia
{
    use AsAction;

    public function handle(?Media $media): mixed
    {
        if (! $media?->exists) {
            return null;
        }

        if ($media->disk === 'local') {
            $path = $media->getPath();

            if (! Storage::disk($media->disk)->exists($media->getPathRelativeToRoot())) {
                return null;
            }

            return response()->download($path, $media->file_name);
        } else {
            return redirect($media->getUrl());
        }

    }
}
