<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait AgroAmbiente
{
    private $headers = [
        'Accept' => 'application/json',
        'Accept-Encoding' => 'gzip, deflate, br',
    ];
    private $urls = [
        'stations' => 'https://agroambiente.regione.abruzzo.it/api/aedita_meteo/get_stations',
        'weather' => 'https://agroambiente.regione.abruzzo.it/api/aedita_meteo/get_data'
    ];


    public function agroAmbienteStations(Request $request)
    {

        $array = array();

        /**** REQUEST ****/
        $response = Http::withHeaders($this->headers)->get($this->urls['stations']);

        if ($response->ok()) {

            $stations = collect($response->json()['data']); //Le stazioni ottenute

            if ($request->query('province') && is_string($request->query('province'))) {
                $province = strtolower($request->query('province'));


                $stations = $stations->filter(function ($value, $key) use ($province) {
                    return strtolower($value['province']) === $province;
                });
            }

            if ($stations->count() > 0) {
                foreach ($stations as $station) { //Inserisco le stazioni dentro

                    $formatted_station[] = [
                        'id' => (string)$station['id_station'],
                        'name' => $station['name'],
                        'code' => $station['cod_station'],
                        'province' => $station['province'],
                        'disabled' => $station['disabled'],
                        'coordinates' => [
                            'lat' => $station['lat'],
                            'lon' => $station['lon'],
                        ]
                    ];
                    $array['data'] = $formatted_station;

                }
                //End foreach
            }


        } elseif ($response->failed()) {
            unset($array['data']);
            $array = [
                'status' => $response->status(),
                'title' => 'External server error',
            ];

        }

        return response()->json($array, 200, ['Content-Type' => 'application/json']);
    }

    public function agroAmbienteStation($id)
    {

        /**** REQUEST ****/
        $response = Http::withHeaders($this->headers)->get($this->urls['stations']);

        if ($response->ok()) {

            $stations = collect($response->json()['data']); //Le stazioni ottenute

            $station = $stations->firstWhere('id_station', $id);

            if (!empty($station)) {
                $formatted_station = [
                    'id' => (string)$station['id_station'],
                    'name' => $station['name'],
                    'code' => $station['cod_station'],
                    'province' => $station['province'],
                    'disabled' => $station['disabled'],
                    'coordinates' => [
                        'lat' => $station['lat'],
                        'lon' => $station['lon'],
                    ]
                ];
                return response()->json($formatted_station, 200, ['Content-Type' => 'application/json']);
            } else {
                return $this->ResponseError(404, 'Not found', 'Station not found');
            }


        } elseif ($response->failed()) {
            $array = [
                'status' => $response->status(),
                'title' => 'External server error',
            ];
            return response()->json($array, 200, ['Content-Type' => 'application/json']);

        }

    }

    public function agroAmbienteStationWeather($id)
    {

        $array = array();
        /**** REQUEST ****/
        $url = $this->urls['weather'] . '?id_station=' . $id . '&date_from=' . $this->from . '&date_to=' . $this->to;
        $response = Http::withHeaders($this->headers)
            ->get($url);

        if ($response->ok()) {

            $data = collect($response->json()['data']);

            if($data->count() > 0){
                foreach ($data as $item){
                    $item_formatted = collect($item)->only('id_station','datetime','tmin','tmax','tavg','rhmin','rhavg','psum','wmax','wavg','wdir','ravg','rsum');
                    $array['data'][] = $item_formatted;
                }
            } else {
                return $this->ResponseError(404,'Not found','Station not found or has not data');
            }

        } elseif ($response->failed()) {
            $array = [
                'status' => $response->status(),
                'title' => 'External server error',
            ];

        }
        return response()->json($array, 200, ['Content-Type' => 'application/json']);
    }
}
