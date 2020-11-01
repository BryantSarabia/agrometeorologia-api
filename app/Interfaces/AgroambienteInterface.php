<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

Interface AgroambienteInterface{

    public function stations(Request $request, $province);
    public function station($id);
    public function indicator($id);
    public function indicators();
    public function stationWeather($id);
    public function indicatorValues($id);
    public function indicatorValue($station_id, $indicator_id);
    public function models();
    public function runModel($station_id, $model_name);
}
