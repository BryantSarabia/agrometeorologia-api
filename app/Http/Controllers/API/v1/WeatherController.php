<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Agroambiente;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    use UtilityMethods, ResponsesJSON;

    public function getStationWeather(Request $request, $id)
    {

        if ($this->validateID($id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid ID');
        }

        $agroambiente = new Agroambiente();

        // Controllo se nella richiesta c'è il parametro "from"
        if ($this->validateDate($request->query('from'))) {
            $agroambiente->setFrom($request->query('from'));
        }

        // Controllo se nella richiesta c'è il parametro "to"
        if ($this->validateDate($request->query('to'))) {
            $agroambiente->setTo($request->query('to'));        }

        // Controllo che
        if ($agroambiente->getFrom() > $agroambiente->getTo()) {
            return $this->ResponseError(400, 'Bad request', "parameter from cannot be bigger than parameter to");
        }

        return $agroambiente->stationWeather($id);

    }
}
