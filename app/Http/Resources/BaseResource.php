<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    protected function formatDate(?string $date = null): ?string
    {
        return $date ? Carbon::createFromFormat('Y-m-d', $date)->format('M d, Y') : null;
    }

    protected function formatCompactDate(?string $date = null): ?string
    {
        return $date ? Carbon::createFromFormat('Y-m-d', $date)->format('M d') : null;
    }
}
