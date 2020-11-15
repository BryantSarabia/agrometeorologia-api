<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Project;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LocationController extends Controller
{

    use UtilityMethods, ResponsesJSON;

    public function index(Request $request)
    {
        $user = (Project::where('api_key', $request->bearerToken())->first())->user;
        $locations = $user->locations;
        $array = collect();
        if ($locations->count() > 0) {
            $locations->each(function ($location) use ($array) {
                $array->push($location->formatResponse());
            });
            $array = Collection::wrap(['data' => $array]);
        } else {
            $array['data'] = [];
        }
        return response()->json($array, 200, ['Content-Type' => 'application/json']);
    }

    public function save(Request $request)
    {

        if (!isset($request->coordinates['lat']) || !$this->validateCoordinate($request->coordinates['lat'])) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lat must be a float number');
        }

        if (!isset($request->coordinates['lon']) || !$this->validateCoordinate($request->coordinates['lon'])) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lon must be a float number');
        }

        // Controllo che il parametro "radius" sia un int (Il raggio deve essere in KM)
        if ($this->validateID($request->radius) || !$request->radius) {
            return $this->ResponseError(400, 'Bad request', "Parameter radius must be number");
        }

        $lat = $request->coordinates['lat'];
        if (abs($lat) > 90) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lat must be a number between -90 and 90');
        }

        $lon = $request->coordinates['lon'];
        if (abs($lon) > 180) {
            return $this->ResponseError(400, 'Bad request', 'Parameter lon must be a number between -180 and 180');
        }

        $user = (Project::where('api_key', $request->bearerToken())->first())->user;


        $location = Location::create([
            'user_id' => $user->id,
            'lat' => $lat,
            'lon' => $lon,
            'radius' => $request->radius
        ]);

        return response()->json($location->formatResponse(), 201, ['Content-Type', 'application/json']);
    }

    public function delete(Request $request, $id)
    {
        $user = (Project::where('api_key', $request->bearerToken())->first())->user;

        $location = Location::find($id);

        if(!$location){
            return $this->ResponseError(404, 'Not found', 'Location not found');
        }

        if ($location->user->id !== $user->id) {
            return $this->ResponseError(401, 'Unauthorized', 'This location does not belongs to you');
        }
        $location->delete();

        return response()->json([], 204);
    }

    public function deleteAll(Request $request)
    {
        $user = (Project::where('api_key', $request->bearerToken())->first())->user;
        $locations = $user->locations;

        if ($locations->count() > 0) {
            $locations->each(function ($location) {
                $location->delete();
            });
            return response()->json([], 204);
        } else {
            return $this->ResponseError(400, 'Bad request', 'You have no locations to delete');
        }
    }
}
