<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Traits\AgroAmbiente;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    use AgroAmbiente, UtilityMethods, ResponsesJSON;

    private $from, $to;

    public function __construct()
    {
        $this->to = date('Y-m-d');
        $this->from = date('Y-m-d', strtotime($this->to . ' - 1 month'));
    }

    public function getIndicators(){

    }
}
