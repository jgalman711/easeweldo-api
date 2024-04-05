<?php

namespace App\Factories;

use App\Models\Biometrics;
use App\Strategies\Biometrics\ZKTecoBiometricsStrategy;
use Exception;

class BiometricsStrategyFactory
{
    public static function createStrategy(Biometrics $biometrics)
    {
        if ($biometrics->provider == Biometrics::ZKTECO_PROVIDER) {
            return new ZKTecoBiometricsStrategy($biometrics);
        } else {
            throw new Exception('Unsupported biometrics service provider.');
        }
    }
}
