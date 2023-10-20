<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Subscription;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use function PHPSTORM_META\map;

class RegistrationService
{
    protected const BUSINESS_ADMIN_ROLE = 'business-admin';

    protected const DEFAULT_TRIAL_PERIOD = 3;

    protected const DEFAULT_TRIAL_EMPLOYEE_COUNT = 1;

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function register(array $input): array
    {
        try {
            DB::beginTransaction();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $company = Company::create([
                'name' =>  $input['company_name'],
                'slug' => strtolower(str_replace(' ', '-', $input['company_name'])),
                'status' => Company::STATUS_TRIAL,
                'email_address' => $user->email_address
            ]);

            $company->users()->attach($user->id);

            $role = Role::where('name', self::BUSINESS_ADMIN_ROLE)->first();
            $user->assignRole($role);

            $freeTrialSubscription = Subscription::where('type', Company::STATUS_TRIAL)->first();

            $data = [
                'subscription_id' => $freeTrialSubscription->id,
                'months' => self::DEFAULT_TRIAL_PERIOD,
                'employee_count' => self::DEFAULT_TRIAL_EMPLOYEE_COUNT
            ];

            $subscription = $this->subscriptionService->subscribe($company, $data);
            DB::commit();
            return [
                'token' => $user->createToken(env('APP_NAME'))->plainTextToken,
                'user' => $user,
                'company' => $company,
                'subscription' => $subscription
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmEmail(string $email): void
    {

    }
}
