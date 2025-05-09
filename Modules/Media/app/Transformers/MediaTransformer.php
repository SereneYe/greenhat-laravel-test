<?php

namespace Modules\Media\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaTransformer extends TransformerAbstract
{
    public function transform($data): array
    {
        $model = $data instanceof Media ? $data : new Media;

        return [
            'id' => $model->getKey(),
            'url' => $model->getKey() ? $model->getUrl() : null,
            'srcset' => $model->getKey() ? $model->getSrcset() : [],
            'customProperties' => $model->custom_properties,
        ];
    }
}
