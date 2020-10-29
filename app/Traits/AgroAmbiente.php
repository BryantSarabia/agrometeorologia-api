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


        } elseif ($response->failed()) {
            $array = [
                'code' => $response->status(),
                'title' => 'External server error',
            ];

        }

        return response()->json($array, 200, ['Content-Type' => 'application/json']);
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
            $array = [
                'code' => $response->status(),
                'title' => 'External server error',
            ];
            return response()->json($array, 200, ['Content-Type' => 'application/json']);

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
            } else {
                return $this->ResponseError(404, 'Not found', 'Station not found or has not data');
            }

        } elseif ($response->failed()) {
            $array = [
                'code' => $response->status(),
                'title' => 'External server error',
            ];

        }
        return response()->json($array, 200, ['Content-Type' => 'application/json']);
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

        } elseif ($response->failed()) {
            $array = [
                'code' => $response->status(),
                'title' => 'External server error',
            ];
        }

        return response()->json($array, 200, ['Content-Type' => 'application/json']);

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
            $array = [
                'code' => $response->status(),
                'title' => 'External server error',
            ];
            return response()->json($array, 200, ['Content-Type' => 'application/json']);
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
        } elseif ($response->failed()) {
            $array = [
                'code' => $response->status(),
                'title' => 'External server error'
            ];
        }

        return response()->json($array, 200, ['Content-Type' => 'application/json']);
    }

    public function agroAmbienteIndicatorValue($station_id, $indicator_id)
    {

        $station = $this->agroAmbienteStation($station_id);
        if(!$station->isOk()){
            return $this->ResponseError($station->getStatusCode(),$station->getData()->title, $station->getData()->details);
        }

        $indicator = $this->agroAmbienteIndicator($indicator_id);
        if(!$indicator->isOk()){
            return $this->ResponseError($indicator->getStatusCode(),$indicator->getData()->title, $indicator->getData()->details);
        }
        $array = array();
        $data = null;
        $response = $this->agroAmbienteIndicatorValues($indicator_id);
        if($response->isOk()){
            $data = collect($response->getData()->data)->firstWhere('station.id',$station_id);
            if(!empty($data)) {
                $array['data'] = $data;
                return response()->json($array, 200, ['Content-Type' => 'application/json']);
            } else {
                return $this->ResponseError(404,'Not found','This station has no data about this indicator');
            }
        }
    }
}
