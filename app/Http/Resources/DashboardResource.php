<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DashboardResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        dd($request->all());
        return parent::toArray($request);
    }
}
