<?php

namespace App\Factories;

use App\Models\Biometrics;
use App\Strategies\Biometrics\ZKTecoBiometricsStrategy;

class BiometricsStrategyFactory
{
    public static function createStrategy(Biometrics $biometrics)
    {
        if ($biometrics->provider == Biometrics::ZKTECO_PROVIDER) {
            return new ZKTecoBiometricsStrategy($biometrics);
        }
    }
}
