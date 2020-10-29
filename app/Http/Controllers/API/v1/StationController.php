<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Traits\AgroAmbiente;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\ResponsesJSON;
use Illuminate\Support\Str;

class StationController extends Controller
{
    use ResponsesJSON, AgroAmbiente, UtilityMethods;

    private $source;

    public function __construct()
    {
        $this->source = 'agroAmbiente';
    }

    public function getStations(Request $request)
    {
        if ($request->query('source') && is_string($request->query('source'))) {
            $this->source = $request->query('source');
        }
        if ($this->source === 'agroAmbiente') {

            return $this->agroAmbienteStations($request);


        } elseif ($this->source === 'other_source') {
            //
        } else {
            return $this->ResponseError(400, 'Bad request', 'Undefined source');
        }

    }

    public function getStation($id)
    {
        if ($this->validateID($id)) {
            return $this->ResponseError(400, 'Bad request', 'Invalid ID');
        }

        return $this->agroAmbienteStation($id);

    }



}
