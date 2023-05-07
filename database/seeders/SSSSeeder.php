<?php

namespace Database\Seeders;

use App\Models\SSS;
use Illuminate\Database\Seeder;

class SSSSeeder extends Seeder
{
    public const MIN_AMOUNT = 1;
    public const MAX_AMOUNT = 999999.99;

    public function run(): void
    {
        SSS::insert($this->sssData);
    }

    public $sssData = [
        ['min_compensation' => self::MIN_AMOUNT, 'max_compensation' => 4249.99, 'employer_contribution' => 390, 'employee_contribution' => 180],
        ['min_compensation' => 4250, 'max_compensation' => 4749.99, 'employer_contribution' => 437.50, 'employee_contribution' => 202.50],
        ['min_compensation' => 4750, 'max_compensation' => 5249.99, 'employer_contribution' => 485, 'employee_contribution' => 225],
        ['min_compensation' => 5250, 'max_compensation' => 5749.99, 'employer_contribution' => 532.50, 'employee_contribution' => 247.50],
        ['min_compensation' => 5750, 'max_compensation' => 6249.99, 'employer_contribution' => 580, 'employee_contribution' => 270],
        ['min_compensation' => 6250, 'max_compensation' => 6749.99, 'employer_contribution' => 627.50, 'employee_contribution' => 292.50],
        ['min_compensation' => 6750, 'max_compensation' => 7249.99, 'employer_contribution' => 675, 'employee_contribution' => 315],
        ['min_compensation' => 7250, 'max_compensation' => 7749.99, 'employer_contribution' => 722.50, 'employee_contribution' => 337.50],
        ['min_compensation' => 7750, 'max_compensation' => 8249.99, 'employer_contribution' => 770, 'employee_contribution' => 360],
        ['min_compensation' => 8250, 'max_compensation' => 8749.99, 'employer_contribution' => 817.50, 'employee_contribution' => 382.50],
        ['min_compensation' => 8750, 'max_compensation' => 9249.99, 'employer_contribution' => 865, 'employee_contribution' => 405],
        ['min_compensation' => 9250, 'max_compensation' => 9749.99, 'employer_contribution' => 912.50, 'employee_contribution' => 427.50],
        ['min_compensation' => 9750, 'max_compensation' => 10249.99, 'employer_contribution' => 960, 'employee_contribution' => 450],
        ['min_compensation' => 10250, 'max_compensation' => 10749.99, 'employer_contribution' => 1007.50, 'employee_contribution' => 472.50],
        ['min_compensation' => 10750, 'max_compensation' => 11249.99, 'employer_contribution' => 1055, 'employee_contribution' => 495],
        ['min_compensation' => 11250, 'max_compensation' => 11749.99, 'employer_contribution' => 1102.50, 'employee_contribution' => 517.50],
        ['min_compensation' => 11750, 'max_compensation' => 12249.99, 'employer_contribution' => 1135, 'employee_contribution' => 540],
        ['min_compensation' => 12250, 'max_compensation' => 12749.99, 'employer_contribution' => 1150, 'employee_contribution' => 562.50],
        ['min_compensation' => 12750, 'max_compensation' => 13249.99, 'employer_contribution' => 1197.50, 'employee_contribution' => 585],
        ['min_compensation' => 13250, 'max_compensation' => 13749.99, 'employer_contribution' => 1245, 'employee_contribution' => 607.50],
        ['min_compensation' => 13750, 'max_compensation' => 14249.99, 'employer_contribution' => 1292.50, 'employee_contribution' => 630],
        ['min_compensation' => 14250, 'max_compensation' => 14749.99, 'employer_contribution' => 1340, 'employee_contribution' => 652.50],
        ['min_compensation' => 14750, 'max_compensation' => 15249.99, 'employer_contribution' => 1387.50, 'employee_contribution' => 675],
        ['min_compensation' => 15250, 'max_compensation' => 15749.99, 'employer_contribution' => 1455, 'employee_contribution' => 697.50],
        ['min_compensation' => 15750, 'max_compensation' => 16249.99, 'employer_contribution' => 1502.50, 'employee_contribution' => 720],
        ['min_compensation' => 16250, 'max_compensation' => 16749.99, 'employer_contribution' => 1597, 'employee_contribution' => 742.50],
        ['min_compensation' => 16750, 'max_compensation' => 17249.99, 'employer_contribution' => 1645, 'employee_contribution' => 765],
        ['min_compensation' => 16750, 'max_compensation' => 17249.99, 'employer_contribution' => 1645, 'employee_contribution' => 765],
        ['min_compensation' => 17250, 'max_compensation' => 17749.99, 'employer_contribution' => 1692.50, 'employee_contribution' => 787.50],
        ['min_compensation' => 17750, 'max_compensation' => 18249.99, 'employer_contribution' => 1740, 'employee_contribution' => 810],
        ['min_compensation' => 18250, 'max_compensation' => 18749.99, 'employer_contribution' => 1787.50, 'employee_contribution' => 832.50],
        ['min_compensation' => 18750, 'max_compensation' => 19249.99, 'employer_contribution' => 1835, 'employee_contribution' => 855],
        ['min_compensation' => 19250, 'max_compensation' => 19749.99, 'employer_contribution' => 1882.50, 'employee_contribution' => 877.50],
        ['min_compensation' => 19750, 'max_compensation' => 20249.99, 'employer_contribution' => 1930, 'employee_contribution' => 900],
        ['min_compensation' => 20250, 'max_compensation' => 20749.99, 'employer_contribution' => 1977.50, 'employee_contribution' => 922.50],
        ['min_compensation' => 20750, 'max_compensation' => 21249.99, 'employer_contribution' => 2025, 'employee_contribution' => 945],
        ['min_compensation' => 21250, 'max_compensation' => 21749.99, 'employer_contribution' => 2072.50, 'employee_contribution' => 967.50],
        ['min_compensation' => 21750, 'max_compensation' => 22249.99, 'employer_contribution' => 2120, 'employee_contribution' => 990],
        ['min_compensation' => 22250, 'max_compensation' => 22749.99, 'employer_contribution' => 2167.50, 'employee_contribution' => 1012.50],
        ['min_compensation' => 22750, 'max_compensation' => 23249.99, 'employer_contribution' => 2215, 'employee_contribution' => 1035],
        ['min_compensation' => 23250, 'max_compensation' => 23749.99, 'employer_contribution' => 2262.50, 'employee_contribution' => 1057.50],
        ['min_compensation' => 23750, 'max_compensation' => 24249.99, 'employer_contribution' => 2310, 'employee_contribution' => 1080],
        ['min_compensation' => 24250, 'max_compensation' => 24749.99, 'employer_contribution' => 2357.50, 'employee_contribution' => 1102.50],
        ['min_compensation' => 24750, 'max_compensation' => 25249.99, 'employer_contribution' => 2405, 'employee_contribution' => 1125],
        ['min_compensation' => 25250, 'max_compensation' => 25749.99, 'employer_contribution' => 2452.50, 'employee_contribution' => 1147.50],
        ['min_compensation' => 25750, 'max_compensation' => 26249.99, 'employer_contribution' => 2500, 'employee_contribution' => 1170],
        ['min_compensation' => 26250, 'max_compensation' => 26749.99, 'employer_contribution' => 2547.50, 'employee_contribution' => 1192.50],
        ['min_compensation' => 26750, 'max_compensation' => 27249.99, 'employer_contribution' => 2595, 'employee_contribution' => 1215],
        ['min_compensation' => 27250, 'max_compensation' => 27749.99, 'employer_contribution' => 2642.50, 'employee_contribution' => 1237.50],
        ['min_compensation' => 27750, 'max_compensation' => 28249.99, 'employer_contribution' => 2690, 'employee_contribution' => 1260],
        ['min_compensation' => 28250, 'max_compensation' => 28749.99, 'employer_contribution' => 2737.50, 'employee_contribution' => 1282.50],
        ['min_compensation' => 28750, 'max_compensation' => 29249.99, 'employer_contribution' => 2785, 'employee_contribution' => 1305],
        ['min_compensation' => 29250, 'max_compensation' => 29749.99, 'employer_contribution' => 2832.50, 'employee_contribution' => 1327.50],
        ['min_compensation' => 29750, 'max_compensation' => self::MAX_AMOUNT, 'employer_contribution' => 2880, 'employee_contribution' => 1350]
    ];
}
