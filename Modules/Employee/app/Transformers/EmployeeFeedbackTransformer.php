<?php

namespace Modules\Employee\Transformers;

use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Modules\Employee\Models\EmployeeFeedback;

class EmployeeFeedbackTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'employee',
    ];

    public function transform($data): array
    {
        $model = $data instanceof EmployeeFeedback ? $data : new EmployeeFeedback;

        return [
            'id' => $model->getKey(),
            'employeeId' => $model->employeeId,
            'name' => $model->name,
            'email' => $model->email,
            'comment' => $model->comment,
            'createdAt' => $model->createdAt,
            'updatedAt' => $model->updatedAt,
        ];
    }

    public function includeEmployee($data): Item
    {
        $model = $data instanceof EmployeeFeedback ? $data : new EmployeeFeedback;

        return $this->item($model->employee(), new EmployeeTransformer);
    }
}
