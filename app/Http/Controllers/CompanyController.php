<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CompanyController extends Controller
{
    use Filter;

    protected const PUBLIC_PATH = 'public/';

    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(Request $request): JsonResponse
    {
        $companies = $this->applyFilters($request, Company::query()->with('setting'), [
            'name',
        ]);
        return $this->sendResponse(CompanyResource::collection($companies), 'Companies retrieved successfully.');
    }

   public function store(CompanyRequest $request): JsonResponse
    {
        $input = $request->validated();
        $input['slug'] = strtolower(str_replace(' ', '-', $input['name']));
        $input['status'] = Company::STATUS_ACTIVE;
        if (isset($input['logo']) && $input['logo']) {
            $filename = time().'.'.$request->logo->extension();
            $request->logo->storeAs('uploads/companies/images', $filename);
            $input['logo'] = $filename;
        } else {
            unset($input['logo']);
        }
        $company = Company::create($input);

        return $this->sendResponse(new CompanyResource($company), 'Company created successfully.');
    }

    public function show(Company $company): JsonResponse
    {
        return $this->sendResponse(new CompanyResource($company), 'Company retrieved successfully.');
    }

    public function update(CompanyRequest $request, Company $company): JsonResponse
    {
        $companyUploadPath = config('app.uploads.company_path');
        $input = $request->validated();
        if (isset($input['logo']) && $input['logo']) {
            if ($company->logo) {
                $filePath = public_path("$companyUploadPath/$company->logo");
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
            $filename = time().'.'.$request->logo->extension();
            $request->logo->storeAs($companyUploadPath, $filename);
            $input['logo'] = $filename;
        } else {
            unset($input['logo']);
        }
        $company->update($input);

        return $this->sendResponse(new CompanyResource($company), 'Company updated successfully.');
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return $this->sendResponse(new CompanyResource($company), 'Company deleted successfully.');
    }
}
