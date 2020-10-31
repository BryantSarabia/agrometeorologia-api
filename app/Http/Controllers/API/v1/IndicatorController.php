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

    public function getIndicators()
    {
        return $this->agroAmbienteIndicators();
    }

    public function getIndicator($id){

        if($this->validateID($id)){
            return $this->ResponseError(400, 'Bad Request', 'Invalid ID');
        }

        return $this->agroAmbienteIndicator($id);
    }

    public function getIndicatorValues(Request $request, $id)
    {

        if ($this->validateID($id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid ID');
        }

        // Controllo se nella richiesta c'è il parametro "from"
        if ($this->validateDate($request->query('from'))) {
            $this->from = $request->query('from');
        }

        // Controllo se nella richiesta c'è il parametro "to"
        if ($this->validateDate($request->query('to'))) {
            $this->to = $request->query('to');
        }

        // Controllo che la data iniziale non sia maggiore che la data finale
        if ($this->from > $this->to) {
            return $this->ResponseError(400, 'Bad request', "parameter from cannot be bigger than parameter to");
        }

        return $this->agroAmbienteIndicatorValues($id);

    }

    public function getIndicatorValue(Request $request, $station_id, $indicator_id)
    {
        if ($this->validateID($station_id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid station ID');
        }

        if ($this->validateID($indicator_id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid indicator ID');
        }

        // Controllo se nella richiesta c'è il parametro "from"
        if ($this->validateDate($request->query('from'))) {
            $this->from = $request->query('from');
        }

        // Controllo se nella richiesta c'è il parametro "to"
        if ($this->validateDate($request->query('to'))) {
            $this->to = $request->query('to');
        }

        // Controllo che
        if ($this->from > $this->to) {
            return $this->ResponseError(400, 'Bad request', "parameter from cannot be bigger than parameter to");
        }

        return $this->agroAmbienteIndicatorValue($station_id, $indicator_id);
    }
}
