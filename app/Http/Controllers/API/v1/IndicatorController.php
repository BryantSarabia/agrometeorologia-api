<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Agroambiente;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    use UtilityMethods, ResponsesJSON;


    public function getIndicators()
    {
        $agroambiente = new Agroambiente();
        return $agroambiente->indicators();
    }

    public function getIndicator($id)
    {

        if ($this->validateID($id)) {
            return $this->ResponseError(400, 'Bad Request', 'Invalid ID');
        }

        $agroambiente = new Agroambiente();
        return $agroambiente->indicator($id);
    }

    public function getIndicatorValues(Request $request, $id)
    {

        $agroambiente = new Agroambiente();

        if ($this->validateID($id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid ID');
        }

        // Controllo se nella richiesta c'è il parametro "from"
        if ($this->validateDate($request->query('from'))) {
            $agroambiente->setFrom($request->query('from'));
        }

        // Controllo se nella richiesta c'è il parametro "to"
        if ($this->validateDate($request->query('to'))) {
            $agroambiente->setTo($request->query('to'));
        }

        // Controllo che la data iniziale non sia maggiore che la data finale
        if ($agroambiente->getFrom() > $agroambiente->getTo()) {
            return $this->ResponseError(400, 'Bad request', "parameter from cannot be bigger than parameter to");
        }


        return $agroambiente->indicatorValues($id);

    }

    public function getIndicatorValue(Request $request, $station_id, $indicator_id)
    {
        $agroambiente = new Agroambiente();

        if ($this->validateID($station_id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid station ID');
        }

        if ($this->validateID($indicator_id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid indicator ID');
        }


        // Controllo se nella richiesta c'è il parametro "from"
        if ($this->validateDate($request->query('from'))) {
            $agroambiente->setFrom($request->query('from'));
        }

        // Controllo se nella richiesta c'è il parametro "to"
        if ($this->validateDate($request->query('to'))) {
            $agroambiente->setTo($request->query('to'));
        }

        // Controllo che la data iniziale non sia maggiore che la data finale
        if ($agroambiente->getFrom() > $agroambiente->getTo()) {
            return $this->ResponseError(400, 'Bad request', "parameter from cannot be bigger than parameter to");
        }

        return $agroambiente->indicatorValue($station_id, $indicator_id);
    }
}
