<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Traits\AgroAmbiente;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

class PestController extends Controller
{
    use AgroAmbiente, UtilityMethods, ResponsesJSON;

    private $from, $to;

    public function __construct()
    {
        $this->to = date('Y-m-d');
        $this->from = date('Y-m-d', strtotime($this->to . ' - 1 month'));
    }

    public function getReports(Request $request){

        // Controllo che il parametro "lat" sia un double
        if(!$this->validateCoordinate($request->query('lat'))){
            return $this->ResponseError(400, 'Bad request', "Parameter lat must be a float number");
        }

        // Controllo che il parametro "lon" sia un double
        if(!$this->validateCoordinate($request->query('lon'))){
            return $this->ResponseError(400, 'Bad request', "Parameter lon must be a float number");
        }

        // Controllo che il parametro "radius" sia un int
        if($this->validateID($request->query('radius')) || !$request->query('radius')){
            return $this->ResponseError(400, 'Bad request', "Parameter radius must be number");
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

        // LOGICA DELLE DISTANZE
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $radius = $request->query('radius');
        $reports = Report::findNearestReports($lat,$lon,$radius,$this->from,$this->to);

        return response()->json($reports,200,['Content-Type' => 'application/json']);
    }

    public function report(Request $request){

        if(!isset($request->coordinates['lat']) || !$this->validateCoordinate($request->coordinates['lat'])){
            return $this->ResponseError(400, 'Bad request', 'Parameter lat must be a float number');
        }

        if(!isset($request->coordinates['lon']) || !$this->validateCoordinate($request->coordinates['lon'])){
            return $this->ResponseError(400, 'Bad request', 'Parameter lon must be a float number');
        }

        if(!$request->name || !is_string($request->name)){
            return $this->ResponseError(400, 'Bad request', 'Name must be a string');
        }
        if(!$request->message || !is_string($request->message)){
            return $this->ResponseError(400, 'Bad request', 'Message must be a string');
        }
        $report = Report::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'message' => $request->message,
            'lat' => $request->coordinates['lat'],
            'lon' => $request->coordinates['lon']
            ]);


        return response()->json($report->formatResponse(), 201, ['Content-Type' => 'application/json']);
    }
}
