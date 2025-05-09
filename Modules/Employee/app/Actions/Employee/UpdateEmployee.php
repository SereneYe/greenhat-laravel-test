<?php

namespace Modules\Employee\Actions\Employee;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Data\Employee\UpdateEmployeeData;
use Modules\Employee\Models\Employee;
use Modules\Employee\Transformers\EmployeeTransformer;
use Modules\User\Actions\User\UpdateUser;
use Spatie\Fractal\Fractal;

class UpdateEmployee
{
    use AsAction;

    public function handle(UpdateEmployeeData $data): Employee
    {
        if ($data->has('userData')) {
            UpdateUser::make()->handle($data->userData);
        }

        $data->employee
            ->update(
                $data->except('employee', 'userData')->toArray()
            );

        $data->employee->refresh();

        return $data->employee;
    }

    public function authorize(ActionRequest $request): bool
    {
        $employee = Employee::findOrFail($request->route('employeeId'));

        $request->merge(['employee' => $employee]);

        return $request->user()->can('updateProfile', $request->employee);
    }

    public function asController(ActionRequest $request): Employee
    {
        $employee = $request->employee;

        $data = UpdateEmployeeData::validateAndCreate([
            ...$request->all(),
            'userData' => [
                ...$request->all(),
                'user' => $employee->user,
            ],
            'employee' => $employee,
        ]);

        return $this->handle($data);
    }

    public function jsonResponse(Employee $data): Fractal
    {
        return fractal($data, EmployeeTransformer::class)
            ->parseIncludes('user');
    }
}
