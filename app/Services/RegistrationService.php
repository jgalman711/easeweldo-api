<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RegistrationService
{
    protected const BUSINESS_ADMIN_ROLE = 'business-admin';

    protected $employeeService;

    public function __construct()
    {
        $this->employeeService = app()->make(EmployeeService::class);
    }

    public function register(array $input): array
    {
        try {
            DB::beginTransaction();
            $input['password'] = bcrypt($input['password']);
            $company = Company::create([
                'name' =>  $input['company_name'],
                'legal_name' => $input['company_name'],
                'contact_name' => $input['first_name'] . " " . $input['last_name'],
                'slug' => strtolower(str_replace(' ', '-', $input['company_name'])),
                'status' => Company::STATUS_PENDING
            ]);
            $user = User::create($input);
            $company->users()->attach($user->id);
            $role = Role::where('name', self::BUSINESS_ADMIN_ROLE)->first();
            $user->assignRole($role);
            $this->employeeService->quickCreate($company, $input);
            DB::commit();
            return [
                'token' => $user->createToken(env('APP_NAME'))->plainTextToken,
                'user' => $user,
                'company' => $company
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
