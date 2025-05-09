<?php

namespace Modules\Employee\Actions\Feedback;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Data\Feedback\CreateEmployeeFeedbackData;
use Modules\Employee\Models\EmployeeFeedback;
use Modules\Employee\Transformers\EmployeeFeedbackTransformer;
use Spatie\Fractal\Fractal;

class CreateEmployeeFeedback
{
    use AsAction;

    public function handle(CreateEmployeeFeedbackData $data): EmployeeFeedback
    {
        $payload = [
            'employeeId' => $data->employee->getKey(),
            'comment' => $data->comment,
            'name' => $data->employee->user->name,
            'email' => $data->employee->user->email,
        ];

        if ($data->has('name')) {
            $payload['name'] = $data->name;
        }

        if ($data->has('email')) {
            $payload['email'] = $data->email;
        }

        return EmployeeFeedback::query()->create($payload);
    }

    public function asController(ActionRequest $request): EmployeeFeedback
    {
        return $this->handle(CreateEmployeeFeedbackData::validateAndCreate([
            ...$request->toArray(),
            'employee' => $request->user()->employee,
        ]));
    }

    public function jsonResponse(EmployeeFeedback $data): Fractal
    {
        return fractal($data, EmployeeFeedbackTransformer::class);
    }
}
