<?php

namespace Database\Seeders;

use App\Models\Pagibig;
use Illuminate\Database\Seeder;

class PagibigSeeder extends Seeder
{
    public const MIN_AMOUNT = 1;
    public const MAX_AMOUNT = 999999.99;

    public $pagibig = [
        [
            'min_compensation' => self::MIN_AMOUNT,
            'max_compensation' => 1499.99,
            'employee_contribution_rate' => 0.02,
            'employer_contribution_rate' => 0.01,
            'status' => PagIbig::ACTIVE
        ], [
            'min_compensation' => 1500,
            'max_compensation' => 4999.99,
            'employee_contribution_rate' => 0.02,
            'employer_contribution_rate' => 0.02,
            'status' => PagIbig::ACTIVE
        ], [
            'min_compensation' => 5000,
            'max_compensation' => self::MAX_AMOUNT,
            'employee_contribution_rate' => 0.03,
            'employer_contribution_rate' => 0.03,
            'status' => PagIbig::ACTIVE
        ]
        
    ];

    public function run(): void
    {
        PagIbig::insert($this->pagibig);
    }
}
