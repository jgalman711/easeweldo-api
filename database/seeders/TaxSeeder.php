<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public const MAX_AMOUNT = 999999.99;
    public const MIN_AMOUNT = 1;

    public function run(): void
    {
        $taxes = [
            [
                'type' => 'weekly',
                'min_compensation' => 1,
                'max_compensation' => 4808,
                'base_tax' => 0,
                'over_compensation_level_rate' => 0
            ], [
                'type' => 'weekly',
                'min_compensation' => 4809,
                'max_compensation' => 7691,
                'base_tax' => 0,
                'over_compensation_level_rate' => 0.15
            ], [
                'type' => 'weekly',
                'min_compensation' => 7692,
                'max_compensation' => 15384,
                'base_tax' => 432.60,
                'over_compensation_level_rate' => 0.20
            ], [
                'type' => 'weekly',
                'min_compensation' => 15385,
                'max_compensation' => 38461,
                'base_tax' => 1971.20,
                'over_compensation_level_rate' => 0.25
            ], [
                'type' => 'weekly',
                'min_compensation' => 38462,
                'max_compensation' => 153845,
                'base_tax' => 7740.45,
                'over_compensation_level_rate' => 0.30
            ], [
                'type' => 'weekly',
                'min_compensation' => 153846,
                'max_compensation' => self::MAX_AMOUNT,
                'base_tax' => 42355.65,
                'over_compensation_level_rate' => 0.35
            ], [
                'type' => 'semi-monthly',
                'min_compensation' => self::MIN_AMOUNT,
                'max_compensation' => 10417,
                'base_tax' => 0,
                'over_compensation_level_rate' => 0
            ], [
                'type' => 'semi-monthly',
                'min_compensation' => 10418,
                'max_compensation' => 16666,
                'base_tax' => 0,
                'over_compensation_level_rate' => 0.15
            ], [
                'type' => 'semi-monthly',
                'min_compensation' => 16667,
                'max_compensation' => 33332,
                'base_tax' => 937.50,
                'over_compensation_level_rate' => 0.20
            ], [
                'type' => 'semi-monthly',
                'min_compensation' => 33333,
                'max_compensation' => 83332,
                'base_tax' => 4270.70,
                'over_compensation_level_rate' => 0.25
            ], [
                'type' => 'semi-monthly',
                'min_compensation' => 83333,
                'max_compensation' => 333332,
                'base_tax' => 16770.70,
                'over_compensation_level_rate' => 0.30
            ], [
                'type' => 'semi-monthly',
                'min_compensation' => 333333,
                'max_compensation' => self::MAX_AMOUNT,
                'base_tax' => 91770.70,
                'over_compensation_level_rate' => 0.35
            ], [
                'type' => 'monthly',
                'min_compensation' => self::MIN_AMOUNT,
                'max_compensation' => 20833,
                'base_tax' => 0,
                'over_compensation_level_rate' => 0
            ], [
                'type' => 'monthly',
                'min_compensation' => 20834,
                'max_compensation' => 33332,
                'base_tax' => 0,
                'over_compensation_level_rate' => 0.15
            ], [
                'type' => 'monthly',
                'min_compensation' => 33333,
                'max_compensation' => 66666,
                'base_tax' => 1875,
                'over_compensation_level_rate' => 0.20
            ], [
                'type' => 'monthly',
                'min_compensation' => 66667,
                'max_compensation' => 166666,
                'base_tax' => 8541.80,
                'over_compensation_level_rate' => 0.25
            ], [
                'type' => 'monthly',
                'min_compensation' => 166667,
                'max_compensation' => 666666,
                'base_tax' => 33541.80,
                'over_compensation_level_rate' => 0.30
            ], [
                'type' => 'monthly',
                'min_compensation' => 666667,
                'max_compensation' => self::MAX_AMOUNT,
                'base_tax' => 183541.80,
                'over_compensation_level_rate' => 0.35
            ]
        ];
        Tax::insert($taxes);
    }
}
