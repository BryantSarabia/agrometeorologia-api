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
        'weather' => 'https://agroambiente.regione.abruzzo.it/api/aedita_meteo/get_data',
        'indicators' => 'https://agroambiente.regione.abruzzo.it/api/aedita_meteo/get_indicator',
        'indicator_values' => 'https://agroambiente.regione.abruzzo.it/api/aedita_meteo/get_report_data/',
        'models' => 'https://agroambiente.regione.abruzzo.it/api/mod_aedita_model/get_stations_models',
        'run_models' => 'https://agroambiente.regione.abruzzo.it/api/aedita/runModel/'
    ];

    /*** Stations Tag***/

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
            return response()->json($array, 200, ['Content-Type' => 'application/json']);

        } elseif ($response->failed()) {
            return $this->ResponseError($response->status(), 'External server error', '');
        }

    }

    public function agroAmbienteStation($id)
    {

        $array = array();

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
                $array['data'] = $formatted_station;
                return response()->json($array, 200, ['Content-Type' => 'application/json']);
            } else {
                return $this->ResponseError(404, 'Not found', 'Station not found');
            }


        } elseif ($response->failed()) {
            return $this->ResponseError($response->status(), 'External server error', '');
        }

    }

    /*** Weather Tag ***/

    public function agroAmbienteStationWeather($id)
    {

        $array = array();
        /**** REQUEST ****/
        $url = $this->urls['weather'] . '?id_station=' . $id . '&date_from=' . $this->from . '&date_to=' . $this->to;
        $response = Http::withHeaders($this->headers)
            ->get($url);

        if ($response->ok()) {

            $data = collect($response->json()['data']);

            if ($data->count() > 0) {
                foreach ($data as $item) {
                    $item_formatted = collect($item)->only('id_station', 'datetime', 'tmin', 'tmax', 'tavg', 'rhmin', 'rhavg', 'psum', 'wmax', 'wavg', 'wdir', 'ravg', 'rsum');
                    $array['data'][] = $item_formatted;
                }
                return response()->json($array, 200, ['Content-Type' => 'application/json']);
            } else {
                return $this->ResponseError(404, 'Not found', 'Station not found or has not data');
            }

        } elseif ($response->failed()) {
            return $this->ResponseError($response->status(), 'External server error', '');
        }

    }

    /*** Indicators Tag ***/

    public function agroAmbienteIndicators()
    {

        $array = collect();

        /*** Request ***/
        $response = Http::withHeaders($this->headers)->get($this->urls['indicators']);

        if ($response->ok()) {
            $indicators = collect($response->json()['data'])->sortBy('id_weather_indicator');

            if ($indicators->count() > 0) {

                foreach ($indicators as $indicator) {
                    $formatted_indicator[] = [
                        'id' => $indicator['id_weather_indicator'],
                        'name' => $indicator['indicator_name'],
                        'ind_group' => $indicator['ind_group']
                    ];

                    $array['data'] = $formatted_indicator;
                }
            }
            return response()->json($array, 200, ['Content-Type' => 'application/json']);
        } elseif ($response->failed()) {
            return $this->ResponseError($response->status(), 'External server error', '');
        }
    }

    public function agroAmbienteIndicator($id)
    {

        $array = array();

        /*** Request ***/
        $response = Http::withHeaders($this->headers)->get($this->urls['indicators']);

        if ($response->ok()) {
            $data = collect($response->json()['data'])->firstWhere('id_weather_indicator', $id);
            if (!empty($data)) {

                $formatted_data = [
                    'id' => $data['id_weather_indicator'],
                    'name' => $data['indicator_name'],
                    'ind_group' => $data['ind_group']
                ];
                $array['data'] = $formatted_data;
                return response()->json($array, 200, ['Content-Type' => 'application/json']);
            } else {
                return $this->ResponseError(404, 'Not found', 'Indicator not found');
            }
        } elseif ($response->failed()) {
            return $this->ResponseError($response->status(), 'External server error', '');
        }
    }

    public function agroAmbienteIndicatorValues($id)
    {

        // Prendo l'indicatore e controllo se esiste
        $item = collect($this->agroAmbienteIndicators()->getData()->data)->firstWhere('id', $id);
        if (!$item) {
            return $this->ResponseError(404, 'Not found', 'Indicator not found');
        }

        $array = array();

        /*** REQUEST ***/
        $url = $this->urls['indicator_values'] . $id . '?request[date_from]=' . $this->from . '&request[date_to]=' . $this->to;
        $response = Http::withHeaders($this->headers)->get($url);

        if ($response->ok()) {

            $indicators = collect($response->json()['data'])->sortBy('id_station');
            foreach ($indicators as $indicator) {
                $formatted_data[] = [
                    'station' => [
                        'id' => (string)$indicator['id_station'],
                        'name' => $indicator['name'],
                        'province' => $indicator['province'],
                        'coordinates' => [
                            'lat' => $indicator['lat'],
                            'lon' => $indicator['lon']
                        ]
                    ],
                    'indicator' => [
                        'id' => (string)$item->id,
                        'name' => $item->name,
                        'value' => $indicator['val']
                    ]
                ];

                $array['data'] = $formatted_data;
            }
            return response()->json($array, 200, ['Content-Type' => 'application/json']);
        } elseif ($response->failed()) {
            return $this->ResponseError($response->status(), 'External server error', '');
        }

    }

    public function agroAmbienteIndicatorValue($station_id, $indicator_id)
    {

        $station = $this->agroAmbienteStation($station_id);
        if (!$station->isOk()) {
            return $this->ResponseError($station->getStatusCode(), $station->getData()->title, $station->getData()->details);
        }

        $indicator = $this->agroAmbienteIndicator($indicator_id);
        if (!$indicator->isOk()) {
            return $this->ResponseError($indicator->getStatusCode(), $indicator->getData()->title, $indicator->getData()->details);
        }
        $array = array();
        $data = null;
        $response = $this->agroAmbienteIndicatorValues($indicator_id);
        if ($response->isOk()) {
            $data = collect($response->getData()->data)->firstWhere('station.id', $station_id);
            if (!empty($data)) {
                $array['data'] = $data;
                return response()->json($array, 200, ['Content-Type' => 'application/json']);
            } else {
                return $this->ResponseError(404, 'Not found', 'This station has no data about this indicator');
            }
        }
    }

    /**** Models Tag ***/

    public function agroAmbienteModels()
    {

        $array = array();

        /*** Request ***/
        $response = Http::withHeaders($this->headers)->get($this->urls['models']);

        if ($response->ok()) {
            $data = collect($response->json()['models']['data']);
            if ($data->count() > 0) {
                foreach ($data as $model) {
                    $formatted_model[] = [
                        'name' => $model['model_name'],
                        'description' => $model['model_description']
                    ];
                    $array['data'] = $formatted_model;
                }
            } else {
                $array['data'] = [];
            }
            return response()->json($array, 200, ['Content-Type' => 'application/json']);
        } elseif ($response->failed()) {
            return $this->ResponseError($response->status(), 'External server error', '');
        }

    }

    public function agroAmbienteRunModel($station_id, $model_name)
    {

        $station_response = $this->agroAmbienteStation($station_id);
        if (!$station_response->isOk()) {
            return $this->ResponseError($station_response->getStatusCode(), $station_response->getData()->title, $station_response->getData()->details);
        }
        $station = $station_response->getData()->data;


        $models_response = $this->agroAmbienteModels();
        if (!$models_response->isOk()) {
            return $this->ResponseError($models_response->getStatusCode(), $models_response->getData()->title, $models_response->getData()->details);
        }
        $model = collect($models_response->getData()->data)->firstWhere('name', $model_name);

        if (!$model) {
            return $this->ResponseError(404, 'Not found', 'Model not found');
        }

        /*** Request ***/

        $array = array();

        $url = $this->urls['run_models'] . $model->name;

        $body = [
            'weather' => [],
            'settings' => [
                'param' => [
                    'id_field' => '-' . $station->id,
                    'id_station' => $station_id,
                    'date_from' => $this->from
                ]
            ]
        ];

        $response = Http::withHeaders($this->headers)
            ->post($url, $body);

        if ($response->ok()) {
            $data = collect($response->json()['results']['values']);

            if ($data->count() > 0) {
                foreach ($data as $result) {
                    $formatted_data[] = [
                        "tavg" => $result['tavg'],
                        "nhh_cum" => $result['nhh_cum'],
                        "nhh" => $result['nhh'],
                        "datetime" => $result['datetime'],
                        "nhh_stage" => $result['nhh_stage'],
                        "nhh_bbch" => $result['nhh_bbch'],
                        "nhh_perc" => $result['nhh_perc'],
                        "nhh_bbch_next" => $result['nhh_bbch_next'],
                        "nhh_stage_next" => $result['nhh_stage_next'],
                        "day_degree" => $result['day_degree'],
                        "stage" => $result['stage']
                    ];

                    $array['data'] = $formatted_data;
                }
            } else {
                $array['data'] = [];
            }
            return response()->json($array,200,['Content-Type' => 'application/json']);
        } elseif($response->failed()){
            return $this->ResponseError($response->status(), 'External server error', '');
        }


    }
}
