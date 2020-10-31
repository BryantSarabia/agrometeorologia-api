<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Traits\AgroAmbiente;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    use AgroAmbiente, UtilityMethods, ResponsesJSON;
    private $from, $to;

    public function __construct()
    {
        $this->to = date('Y-m-d');
        $this->from = date('Y-m-d', strtotime($this->to . ' - 1 month'));
    }

    public function getModels(){
        return $this->agroAmbienteModels();
    }

    public function runModel(Request $request, $station_id, $model_name){

        if ($this->validateID($station_id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid ID');
        }

        if(!is_string($model_name)){
            return $this->ResponseError(400, 'Bad request', 'Invalid model name');
        }

        // Controllo se nella richiesta c'è il parametro "from"
        if ($this->validateDate($request->query('from'))) {
            $this->from = $request->query('from');
        }


        return $this->agroAmbienteRunModel($station_id, $model_name);
    }
}
