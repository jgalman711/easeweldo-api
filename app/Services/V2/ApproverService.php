<?php

namespace App\Services\V2;

use App\Models\Approver;

class ApproverService
{
    public function createOrUpdate(array $input): Approver
    {
        $newOrder = $input['order'] ?? null;

        $approver = Approver::where([
            'employee_id' => $input['employee_id'],
            'requester_employee_id' => $input['requester_employee_id'],
            'request_type' => $input['request_type']
        ])->first();
        
        if ($approver) {
            return $approver;
        }

        if ($newOrder) {
            Approver::where('requester_employee_id', $input['requester_employee_id'])
                ->where('order', '>=', $newOrder)
                ->increment('order');
        } else {
            $maxOrder = Approver::where('requester_employee_id', $input['requester_employee_id'])->max('order');
            $input['requester_employee_id'] = $maxOrder + 1;
        }

        return Approver::create($input);
    }
}
