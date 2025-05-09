<?php

namespace Modules\Employee\Transformers;

use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Modules\Employee\Models\Employee;
use Modules\User\Transformers\UserTransformer;

class EmployeeTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'user',
    ];

    public function transform($data): array
    {
        $model = $data instanceof Employee ? $data : new Employee;

        return [
            'id' => $model->getKey(),
            'userId' => $model->userId,
            'role' => $model->role,
            'createdAt' => $model->createdAt,
            'updatedAt' => $model->updatedAt,
        ];
    }

    public function includeUser($data): Item
    {
        $model = $data instanceof Employee ? $data : new Employee;

        return $this->item($model->user, new UserTransformer);
    }
}
