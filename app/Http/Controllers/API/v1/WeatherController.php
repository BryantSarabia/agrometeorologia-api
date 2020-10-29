<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Traits\AgroAmbiente;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    use UtilityMethods, AgroAmbiente, ResponsesJSON;

    private $from, $to;

    public function __construct()
    {
        $this->to = date('Y-m-d');
        $this->from = date('Y-m-d', strtotime($this->to . ' - 1 month'));
    }

    public function getStationWeather(Request $request, $id)
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

        // Controllo che
        if ($this->from > $this->to) {
            return $this->ResponseError(400, 'Bad request', "parameter from cannot be bigger than parameter to");
        }

        return $this->agroAmbienteStationWeather($id);

    }
}
