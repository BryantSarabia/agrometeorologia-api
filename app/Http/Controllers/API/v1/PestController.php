<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Mail\PestReports;
use App\Models\Project;
use App\Models\Report;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PestController extends Controller
{
    use UtilityMethods, ResponsesJSON;

    private $from, $to;

    public function __construct()
    {
        $this->to = date('Y-m-d');
        $this->from = date('Y-m-d', strtotime($this->to . ' - 1 month'));
    }

    public function index()
    {
        $reports = Report::recent()->get();

        $array = collect();

        if ($reports->count() > 0) {
            $reports->each(function ($report) use ($array) {
                $array->push($report->formatResponse($report->distance));
            });
            $array = Collection::wrap(['data' => $array]);

        } else {
            $array['data'] = [];
        }
        return response()->json($array, 200, ['Content-Type' => 'application/json']);
    }

    public function getReports(Request $request)
    {

        // Controllo che il parametro "lat" sia un double
        if (!$this->validateCoordinate($request->query('lat'))) {
            return $this->ResponseError(400, 'Bad request', "Parameter lat must be a float number");
        }

        // Controllo che il parametro "lon" sia un double
        if (!$this->validateCoordinate($request->query('lon'))) {
            return $this->ResponseError(400, 'Bad request', "Parameter lon must be a float number");
        }

        // Controllo che il parametro "radius" sia un int (Il raggio deve essere in KM)
        if ($this->validateID($request->query('radius')) || !$request->query('radius')) {
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
        if (abs($lat) > 90) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lat must be a number between -90 and 90');
        }

        $lon = $request->query('lon');
        if (abs($lon) > 180) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lon must be a number between -180 and 180');
        }

        $radius = $request->query('radius');
        $reports = Report::findNearestReports($lat, $lon, $radius, $this->from, $this->to);

        return response()->json($reports, 200, ['Content-Type' => 'application/json']);
    }

    public function report(Request $request)
    {


        if (!isset($request->coordinates['lat']) || !$this->validateCoordinate($request->coordinates['lat'])) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lat must be a float number');
        }

        if (!isset($request->coordinates['lon']) || !$this->validateCoordinate($request->coordinates['lon'])) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lon must be a float number');
        }

        if (!$request->name || !is_string($request->name)) {
            return $this->ResponseError(400, 'Bad request', 'Name must be a string');
        }


        if (!$request->message || !is_string($request->message)) {
            return $this->ResponseError(400, 'Bad request', 'Message must be a string');
        }

        $lat = $request->coordinates['lat'];
        if (abs($lat) > 90) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lat must be a number between -90 and 90');
        }

        $lon = $request->coordinates['lon'];
        if (abs($lon) > 180) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lon must be a number between -180 and 180');
        }


        $user_id = (Project::where('api_key', $request->bearerToken())->first())->user->id;

        $report = Report::create([
            'user_id' => $user_id,
            'name' => $request->name,
            'message' => $request->message,
            'lat' => $lat,
            'lon' => $lon,
            'created_at' => date('Y-m-d')
        ]);

        $locations = $report->findNearestLocations($report->lat, $report->lon);
        if ($locations->count() > 0) {
            $users = collect();
            $reports = collect();
            $reports->push($report);
            $locations->each(function ($location) use ($users) {
                $users->push($location->user);
            });
            $users = $users->unique();
            $users->each(function ($user) use ($reports, $user_id) {
                if ($user->id != $user_id) {
                    Mail::to($user)->queue(new PestReports($user, $reports));
                }
            });
        }

        return response()->json($report->formatResponse(), 201, ['Content-Type' => 'application/json']);
    }
}
