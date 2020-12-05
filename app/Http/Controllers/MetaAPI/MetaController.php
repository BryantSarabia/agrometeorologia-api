<?php

namespace App\Http\Controllers\MetaAPI;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MetaController extends Controller
{

    public function save(Request $request)
    {

        // Controllo se esistono le proprietà nelle configurazioni
        $conf = $request->configuration;
        if (!key_exists('group', $conf)) {
            return "missing group";
        } elseif (!key_exists('service', $conf)) {
            return "missing service";
        } elseif (!key_exists('operations', $conf)) {
            return "missing operations";
        }

        // Controllo le proprieta di ogni source
        foreach ($conf['operations'] as $key => $operation) {
            if (!key_exists('sources', $conf['operations'][$key]) || !key_exists('result', $conf['operations'][$key])) {
                return "missing parameters at " . $key;
            }
            foreach ($operation['sources'] as $source) {
                if (!filter_var($source['urlTemplate'], FILTER_VALIDATE_URL)) {
                    return "invalid source url";
                }

                if (!is_string($source['description'])) {
                    return "description must be a string";
                } elseif (!filter_var($source['required'], FILTER_VALIDATE_BOOL)) {
                    return "source required must be a boolean";
                }
            }

        }

        $group = $conf['group'];
        $service = $conf['service'];
        $operations = $conf['operations'];

        $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
        if ($obj) {
            return "This conf already exists";
        }

        $routeFile = fopen(base_path() . '\routes\meta-api.php', "a");
        foreach ($operations as $key => $operation) {
            $newRoute = "Route::get('/services/" . $group . '/' . $service . '/' . $key . "','MetaController@get');\n";
            fwrite($routeFile, $newRoute);
        }
        fclose($routeFile);

        $obj = MetaApiConfiguration::create([
            'configuration' => json_encode($conf),
        ]);

    }

    public function get(Request $request)
    {
        $path_array = explode('/', $request->path());
        $obj = MetaApiConfiguration::where('configuration->group', $path_array[2])->where('configuration->service', $path_array[3])->first();
        $conf = json_decode($obj->configuration, true);

        // Controllo se la configurazione ha parametri
        if (key_exists('params', $conf['operations'][$path_array[4]])) {
            // Controllo se c'è almeno un parametro dentro params
            if (count($conf['operations'][$path_array[4]]['params']) > 0) {
                $query_params = $request->query(); // Query params
                $params = $conf['operations'][$path_array[4]]['params']; // Parametri della configurazione
                foreach ($params as $key => $param) {
                    // Controllo se i parametri required sono nella richiesta
                    if ($param['required']) {
                        if (!key_exists($key, $query_params)) {
                            return "Missing param: " . $key;
                        }
                        // Setto i parametri non required al valore di default se non sono nella richiesta
                    } elseif (!$param['required'] && !key_exists($key, $query_params)) {
                        $query_params[$key] = $param['default'];
                    }
                    // Controllo il tipo del parametro
                    if (!$this->validateType($param['type'], $query_params[$key])) {
                        return $key . " must be a " . $param['type'];
                    }

                    // Controllo i limiti
                    if (!$this->validateLimits($param, $query_params[$key])) {
                        return $key . " has exceed the limits";
                    }
                    $$key = $query_params[$key];
                }
            }
        }
        // process source
        $sources = $conf['operations'][$path_array[4]]['sources'];
        $results = [];
        foreach ($sources as $key => $source) {

            $url = $source['urlTemplate'];
            eval("\$url = \"$url\";"); // valuto la url
            if (filter_var($url, FILTER_VALIDATE_URL)) { // mi assicuro che la url valutata sia sempre una url valida
                try {
                    $response = Http::timeout(5)->get($url);
                    if (!$response->ok() && $source['required']) {
                        return $key . " failed";
                    }
                    if (key_exists('data', $response->json())) {
                        $results[$key] = $response->json()['data'];
                    } else {
                        $results[$key] = $response->json();
                    }
                } catch (ConnectionException $e) {
                    return $key . " failed";
                }

            }
        }
        return response()->json([
            $results
        ]);
    }

    public function delete(MetaApiConfiguration $configuration)
    {
        $configuration->delete();
        return redirect()->route('admin.configuration.all');
    }

    public function validateType($type, $value)
    {
        if ($value !== null) {
            switch ($type) {
                case 'string':
                    return is_string($value);
                    break;
                case 'integer':
                    return filter_var($value, FILTER_VALIDATE_INT);
                    break;
                case 'float':
                    return filter_var($value, FILTER_VALIDATE_FLOAT);
                    break;
                case 'boolean':
                    return filter_var($value, FILTER_VALIDATE_BOOL);
                    break;
            }
        }
    }

    public function validateLimits($param, $value)
    {
        if ($value !== null) {
            if (key_exists('minimum', $param)) {
                if ($value < $param['minimum']) {
                    return false;
                }
            }

            if (key_exists('maximum', $param)) {
                if ($value > $param['maximum']) {
                    return false;
                }
            }
        }
        return true;
    }
}
