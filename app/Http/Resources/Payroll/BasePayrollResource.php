<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class BasePayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $resource = match ($request->format) {
            'edit' => new EditPayrollResource($this->resource),
            'details' => new DetailsPayrollResource($this->resource),
            default => new DefaultPayrollResource($this->resource)
        };

        return $resource->toArray($request);
    }
}
