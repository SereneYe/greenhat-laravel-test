<?php

namespace Modules\User\Transformers;

use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Modules\Employee\Transformers\EmployeeTransformer;
use Modules\User\Models\User;

class UserTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'employee',
    ];

    public function transform($data): array
    {
        $model = $data instanceof User ? $data : new User;

        $result = [
            'id' => $model->getKey(),
            'email' => $model->email,
            'firstName' => $model->firstName,
            'lastName' => $model->lastName,
            'name' => $model->name,
            'updatedAt' => $model->updatedAt,
            'createdAt' => $model->createdAt,
            'lastLoginAt' => $model->lastLoginAt(),
        ];

        if ($model->wasRecentlyCreated) {
            $result['accessToken'] = $model->createToken('register')->plainTextToken;
        }

        return $result;
    }

    public function includeEmployee($data): Item
    {
        $model = $data instanceof User ? $data : new User;

        return $this->item($model->employee, new EmployeeTransformer);
    }
}
