<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Agroambiente;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    use UtilityMethods, ResponsesJSON;

    public function getModels(){
        $agroambiente = new Agroambiente();
        return $agroambiente->models();
    }

    public function runModel(Request $request, $station_id, $model_name){


        if ($this->validateID($station_id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid ID');
        }

        if(!is_string($model_name)){
            return $this->ResponseError(400, 'Bad request', 'Invalid model name');
        }

        $agroambiente = new Agroambiente();

        // Controllo se nella richiesta c'è il parametro "from"
        if ($this->validateDate($request->query('from'))) {
            $agroambiente->setFrom($request->query('from'));
        }

        return $agroambiente->runModel($station_id, $model_name);
    }
}
