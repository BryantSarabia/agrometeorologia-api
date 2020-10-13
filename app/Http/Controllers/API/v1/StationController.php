<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\ResponsesJSON;

class StationController extends Controller
{
    use ResponsesJSON;

    public function index(Request $request)
    {
        $urls = [
            'agroambiente' => 'https://agroambiente.regione.abruzzo.it/api/aedita_meteo/get_stations',
        ];

        $array = array(
            'agroambiente' => [
                'data' => [], 'meta' => []
            ],
        );

        /**** REQUEST ****/
        $response = Http::withHeaders([
            'Accept' => 'application/json, text/javascript, */*',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive'
        ])->get($urls['agroambiente']);

        if ($response->ok()) {
            $data = []; // Array dove andrò a mettere le stazioni

            $stations = collect($response->json()['data']); //Le stazioni ottenute
            foreach ($stations as $station) { //Inserisco le stazioni dentro $data
                // JSON:API SPECIFICATION https://jsonapi.org/

                $data[] = [
                    'type' => 'station',
                    'id' => (string)$station['id_station'],
                    'attributes' =>
                        array_splice($station, 1) //Rimuovo l'id della stazione dagli attributi
                ];
            }
            //End foreach

            $array['agroambiente']['data'] = $data;
            $array['agroambiente']['meta'] = [
                'source' => $urls['agroambiente']
            ];
            return response()->json($array, 200, ['Content-Type' => 'application/vnd.api+json']);

        } elseif ($response->failed()) {
            unset($array['agroambiente']['data']);
            $array['agroambiente']['errors'] = [
                'status' => $response->status(),
                'title' => 'External server error',
            ];
            return response()->json($array, $response->status(), ['Content-Type' => 'application/vnd.api+json']);
        }
    }
}
