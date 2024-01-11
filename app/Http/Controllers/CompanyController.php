<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    use Filter;

    protected const PUBLIC_PATH = 'public/';

    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * @OA\Get(
     *     path="/api/companies",
     *     summary="Get all companies",
     *     security={{"bearerAuth":{}}},
     *     tags={"Companies"},
     *     @OA\Response(response="200", description="Companies retrieved successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $companies = $this->applyFilters($request, Company::query(), [
            'name'
        ]);
        return $this->sendResponse(CompanyResource::collection($companies), 'Companies retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/companies",
     *     summary="Register a new company",
     *     security={{"bearerAuth":{}}},
     *     tags={"Companies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     maxLength=255,
     *                     description="The name of the company (required, unique)"
     *                 ),
     *                 @OA\Property(
     *                     property="legal_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="The legal name of the company"
     *                 ),
     *                 @OA\Property(
     *                     property="logo",
     *                     type="string",
     *                     format="binary",
     *                     description="Company logo image file (
     *                          nullable,
     *                          max size: 2048 bytes,
     *                          allowed formats: jpeg, png, jpg, gif, svg
     *                     )"
     *                 ),
     *                 @OA\Property(
     *                     property="address_line",
     *                     type="string",
     *                     nullable=true,
     *                     description="Address line of the company"
     *                 ),
     *                 @OA\Property(
     *                     property="barangay_town_city_province",
     *                     type="string",
     *                     nullable=true,
     *                     description="Barangay, town, city, or province of the company"
     *                 ),
     *                 @OA\Property(
     *                     property="contact_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="Contact name for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="email_address",
     *                     type="string",
     *                     format="email",
     *                     nullable=true,
     *                     description="Email address for the company (nullable, unique)"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_number",
     *                     type="string",
     *                     nullable=true,
     *                     description="Mobile number for the company (nullable, PH mobile number format)"
     *                 ),
     *                 @OA\Property(
     *                     property="landline_number",
     *                     type="string",
     *                     nullable=true,
     *                     description="Landline number for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="Bank name for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_account_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="Bank account name for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_account_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="Bank account number for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="tin",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="TIN (Tax Identification Number) for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="sss_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="SSS (Social Security System) number for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="philhealth_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="PhilHealth number for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="pagibig_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="Pag-IBIG (Home Development Mutual Fund) number for the company (max length: 30)"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Company created successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Company created successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Eksa Corporation 3"),
     *                     @OA\Property(property="slug", type="string", example="eksa-corporation-3"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(
     *                          property="updated_at",
     *                          type="string",
     *                          format="date-time",
     *                          example="2024-01-10 15:58:09"
     *                     ),
     *                     @OA\Property(
     *                          property="created_at",
     *                          type="string",
     *                          format="date-time",
     *                          example="2024-01-10 15:58:09"
     *                     ),
     *                     @OA\Property(property="id", type="integer", example=88),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=false),
     *                 @OA\Property(property="message", type="string", example="The given data was invalid."),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                          property="name",
     *                          type="array",
     *                          @OA\Items(
     *                              type="string",
     *                              example="['The name field is required.']"
     *                          )
     *                     ),
     *                     example={"name": "['The name field is required.']"}
     *                 ),
     *             )
     *         )
     *     ),
     * )
     */
    public function store(CompanyRequest $request): JsonResponse
    {
        $input = $request->validated();
        $input['slug'] = strtolower(str_replace(' ', '-', $input['name']));
        $input['status'] = Company::STATUS_ACTIVE;
        if (isset($input['logo']) && $input['logo']) {
            $filename = time() . '.' . $request->logo->extension();
            $request->logo->storeAs(Company::ABSOLUTE_STORAGE_PATH, $filename);
            $input['logo'] = Company::STORAGE_PATH . $filename;
        }
        $company = Company::create($input);
        return $this->sendResponse(new CompanyResource($company), 'Company created successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}",
     *     summary="Get company by slug",
     *     security={{"bearerAuth":{}}},
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Companies retrieved successfully"),
     *     @OA\Response(response="404", description="Company not found"),
     * )
     */
    public function show(Company $company): JsonResponse
    {
        return $this->sendResponse(new CompanyResource($company), 'Company retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}?_method=PUT",
     *     summary="Update company details by slug",
     *     security={{"bearerAuth":{}}},
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     maxLength=255,
     *                     description="The name of the company (required, unique)"
     *                 ),
     *                 @OA\Property(
     *                     property="legal_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="The legal name of the company"
     *                 ),
     *                 @OA\Property(
     *                     property="logo",
     *                     type="string",
     *                     format="binary",
     *                     description="Company logo image file (
     *                          nullable,
     *                          max size: 2048 bytes,
     *                          allowed formats: jpeg, png, jpg, gif, svg
     *                     )"
     *                 ),
     *                 @OA\Property(
     *                     property="address_line",
     *                     type="string",
     *                     nullable=true,
     *                     description="Address line of the company"
     *                 ),
     *                 @OA\Property(
     *                     property="barangay_town_city_province",
     *                     type="string",
     *                     nullable=true,
     *                     description="Barangay, town, city, or province of the company"
     *                 ),
     *                 @OA\Property(
     *                     property="contact_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="Contact name for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="email_address",
     *                     type="string",
     *                     format="email",
     *                     nullable=true,
     *                     description="Email address for the company (nullable, unique)"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_number",
     *                     type="string",
     *                     nullable=true,
     *                     description="Mobile number for the company (nullable, PH mobile number format)"
     *                 ),
     *                 @OA\Property(
     *                     property="landline_number",
     *                     type="string",
     *                     nullable=true,
     *                     description="Landline number for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="Bank name for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_account_name",
     *                     type="string",
     *                     nullable=true,
     *                     description="Bank account name for the company"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_account_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="Bank account number for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="tin",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="TIN (Tax Identification Number) for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="sss_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="SSS (Social Security System) number for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="philhealth_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="PhilHealth number for the company (max length: 30)"
     *                 ),
     *                 @OA\Property(
     *                     property="pagibig_number",
     *                     type="string",
     *                     nullable=true,
     *                     maxLength=30,
     *                     description="Pag-IBIG (Home Development Mutual Fund) number for the company (max length: 30)"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Company created successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Company created successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Eksa Corporation 3"),
     *                     @OA\Property(property="slug", type="string", example="eksa-corporation-3"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(
     *                          property="updated_at",
     *                          type="string",
     *                          format="date-time",
     *                          example="2024-01-10T15:58:09.000000Z"
     *                     ),
     *                     @OA\Property(
     *                          property="created_at",
     *                          type="string",
     *                          format="date-time",
     *                          example="2024-01-10T15:58:09.000000Z"
     *                     ),
     *                     @OA\Property(property="id", type="integer", example=88),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=false),
     *                 @OA\Property(property="message", type="string", example="The given data was invalid."),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                          property="name",
     *                          type="array",
     *                          @OA\Items(
     *                              type="string",
     *                              example="['The name field is required.']"
     *                          )
     *                     ),
     *                     example={"name": "['The name field is required.']"}
     *                 ),
     *             )
     *         )
     *     ),
     * )
     */
    public function update(CompanyRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        if (isset($input['logo']) && $input['logo']) {
            if ($company->logo) {
                Storage::delete(self::PUBLIC_PATH . $company->logo);
            }
            $filename = time() . '.' . $request->logo->extension();
            $request->logo->storeAs(Company::ABSOLUTE_STORAGE_PATH, $filename);
            $input['logo'] = Company::STORAGE_PATH . $filename;
        } else {
            unset($input['logo']);
        }
        $company->update($input);
        return $this->sendResponse(new CompanyResource($company), 'Company updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/companies/{company-slug}",
     *     summary="Delete a company by slug",
     *     security={{"bearerAuth":{}}},
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="204", description="Company deleted successfully"),
     *     @OA\Response(response="404", description="Company not found"),
     * )
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();
        return $this->sendResponse(new CompanyResource($company), 'Company deleted successfully.');
    }
}
