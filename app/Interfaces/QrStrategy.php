<?php

namespace App\Interfaces;

use Illuminate\Support\HtmlString;

interface QrStrategy
{
    public function generate(): HtmlString;
}
