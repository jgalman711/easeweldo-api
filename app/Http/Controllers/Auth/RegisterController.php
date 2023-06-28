<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\CompanyRegistrationRequest;
use App\Models\Company;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RegisterController extends BaseController
{
    protected const BUSINESS_ADMIN_ROLE = 'business-admin';

    public function register(CompanyRegistrationRequest $request)
    {
        $input = $request->validated();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $company = Company::create([
            'name' =>  $input['company_name'],
            'slug' => strtolower(str_replace(' ', '-', $input['company_name'])),
            'status' => Company::STATUS_TRIAL
        ]);

        $role = Role::where('name', self::BUSINESS_ADMIN_ROLE)->first();
        $user->assignRole($role);

        $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
        $success['company_name'] = $company->name;
        return $this->sendResponse($success, 'Company registered successfully.');
    }
}
