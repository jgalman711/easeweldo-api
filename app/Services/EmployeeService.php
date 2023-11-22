<?php

namespace App\Services;

use App\Http\Requests\EmployeeRequest;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
    protected const PUBLIC_PATH = 'public/';

    public function create(Company $company, array $data): Employee
    {
        try {
            DB::beginTransaction();
            $data['company_id'] = $company->id;
            $data['company_employee_id'] = $this->generateCompanyEmployeeId($company);
            $data['status'] = $company->isInSettlementPeriod() ? Employee::PENDING : Employee::ACTIVE;
            $employee = Employee::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $employee;
    }

    public function update(EmployeeRequest $request, Company $company, Employee $employee): Employee
    {
        $input = $request->validated();
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            if ($employee->profile_picture) {
                Storage::delete(self::PUBLIC_PATH . $employee->profile_picture);
            }
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs(Employee::ABSOLUTE_STORAGE_PATH, $filename);
            $input['profile_picture'] = Employee::STORAGE_PATH . $filename;
        } else {
            unset($input['profile_picture']);
        }

        if ($company->isInSettlementPeriod()) {
            $input['status'] = $employee->status;
        }

        $employee->update($input);
        if ($employee->user) {
            $employee->user->update($input);
        }
        return $employee;
    }

    private function generateCompanyEmployeeId(Company $company): int
    {
        $latestEmployee = $company->employees()->orderByDesc('id')->first();
        return $latestEmployee ? $latestEmployee->company_employee_id + 1 : 1;
    }
}
