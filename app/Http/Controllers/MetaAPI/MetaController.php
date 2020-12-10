<?php

namespace App\Http\Controllers\MetaAPI;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class MetaController extends Controller
{
    use ResponsesJSON, UtilityMethods;

    public function __construct()
    {
        $this->middleware('meta-api')->except('get');
        $this->middleware('token')->only('get');
    }

    public function save(Request $request)
    {

        if (strpos($request->header('Content-Type'), 'multipart/form-data') !== false) {
            $rules = ['configuration_file' => 'required|max:4096'];
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->ResponseError(422, 'Validation error', $validator->getMessageBag()->toArray()['configuration_file'][0]);
            }
            $file = $request->file('configuration_file');
            if ($file->getClientMimeType() !== 'application/json') {
                return $this->ResponseError(400, 'Bad request', 'The file must be a JSON file');
            }

            $file_content = file_get_contents($file);
            $conf = json_decode($file_content, true);

        } elseif ($request->header('Content-Type') === "application/json") {
            // Controllo se esistono le proprietà nelle configurazioni
            $conf = $request->all();

        } else {
            return $this->ResponseError(400, 'Bad request', 'Missing configuration');
        }


        if (!is_array($conf)) {
            return $this->ResponseError(400, 'Bad request', 'Parsing error');
        }

        if (!key_exists('group', $conf)) {
            return $this->ResponseError(400, 'Bad request', 'Missing group');
        } elseif (!key_exists('service', $conf)) {
            return $this->ResponseError(400, 'Bad request', 'Missing service');
        } elseif (!key_exists('operations', $conf)) {
            return $this->ResponseError(400, 'Bad request', 'Missing operations');
        }

        // Controllo le proprieta di ogni source
        foreach ($conf['operations'] as $key => $operation) {
            if (!key_exists('sources', $conf['operations'][$key]) || !key_exists('result', $conf['operations'][$key])) {
                return $this->ResponseError(400, 'Bad request', "Missing parameters at {$key}");
            }

            if (key_exists('params', $operation)) {
                foreach ($operation['params'] as $key => $param) {
                    if (!key_exists('required', $param)) {
                        return $this->ResponseError(400, 'Bad request', "Missing required parameter at {$key}");
                    }

                    if (!key_exists('type', $param)) {
                        return $this->ResponseError(400, 'Bad request', "Missing type parameter at {$key}");
                    }

                    if(key_exists('description',$param)){
                        if(!$this->validateType("string", $param['description'])){
                            return $this->ResponseError(400,'Bad request', "Description parameter must be a string at {$key}");
                        }
                    }

                    if (!$this->validateType("boolean", $param['required'])) {
                        return $this->ResponseError(400, 'Bad request', "Required paramter must be a boolean at {$key}");
                    }

                    if(!$this->validateType("string", $param['type'])){
                        return $this->ResponseError(400,'Bad request', "Type parameter must be a string at {$key}");
                    }
                }
            }
            foreach ($operation['sources'] as $key => $source) {
                if (!filter_var($source['urlTemplate'], FILTER_VALIDATE_URL)) {
                    return $this->ResponseError(400, 'Bad request', "Invalid source url at {$key}");
                }

                if (!is_string($source['description'])) {
                    return $this->ResponseError(400, 'Bad request', "Description must be a string at {$key}");
                } elseif (!filter_var($source['required'], FILTER_VALIDATE_BOOL)) {
                    return $this->ResponseError(400, 'Bad request', "Required must be a boolean at {$key}");
                }
            }
        }
        $group = $conf['group'];
        $service = $conf['service'];

        $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
        if ($obj) {
            return $this->ResponseError(400, 'Bad request', 'This configuration already exists');
        }

        $obj = MetaApiConfiguration::create([
            'configuration' => json_encode($conf),
        ]);
        return response()->json(['data' => $obj], 201, ['Content-Type' => 'application/json']);

    }

    public function get(Request $request, $group, $service, $operation)
    {
//        $path_array = explode('/', $request->path());
        if (preg_match("/[^a-zA-Z]+/", $group)) {
            return $this->ResponseError(400, 'Bad request', 'Group is invalid');
        } elseif (preg_match("/[^a-zA-Z]+/", $service)) {
            return $this->ResponseError(400, 'Bad request', 'Service is invalid');
        } elseif (preg_match("/[^a-zA-Z]+/", $operation)) {
            return $this->ResponseError(400, 'Bad request', 'Operation is invalid');
        }

        $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
        if (!$obj) {
            return $this->ResponseError(404, 'Not found', 'Configuration not found');
        }

        $conf = json_decode($obj->configuration, true);

        if (!key_exists($operation, $conf['operations'])) {
            return $this->ResponseError(404, 'Not found', 'Operation not found');
        }

        // Controllo se la configurazione ha parametri
        if (key_exists('params', $conf['operations'][$operation])) {
            // Controllo se c'è almeno un parametro dentro params
            if (count($conf['operations'][$operation]['params']) > 0) {
                $query_params = $request->query(); // Query params
                $params = $conf['operations'][$operation]['params']; // Parametri della configurazione
                foreach ($params as $key => $param) {
                    // Controllo se i parametri required sono nella richiesta
                    if ($param['required']) {
                        if (!key_exists($key, $query_params)) {
                            return $this->ResponseError(400, 'Bad request', "Missing param: {$key}");
                        }
                        // Setto i parametri non required al valore di default se non sono nella richiesta
                    } elseif (!$param['required'] && !key_exists($key, $query_params)) {
                        $query_params[$key] = $param['default'];
                    }
                    // Controllo il tipo del parametro
                    if (!$this->validateType($param['type'], $query_params[$key])) {
                        return $this->ResponseError(400, 'Bad request', "{$key} must be a {$param['type']}");
                    }

                    // Controllo i limiti
                    if (!$this->validateLimits($param, $query_params[$key])) {
                        return $this->ResponseError(400, 'Bad request', "{$key} has exceed the limits");
                    }
                    $$key = $query_params[$key];
                }
            }
        }
        // process source
        $sources = $conf['operations'][$operation]['sources'];
        $results = [];
        foreach ($sources as $key => $source) {

            $url = $source['urlTemplate'];

            eval("\$url = \"$url\";"); // valuto la url
            if (filter_var($url, FILTER_VALIDATE_URL)) { // mi assicuro che la url valutata sia sempre una url valida
                try {
                    $response = Http::timeout(5)->get($url);
                    if (!$response->ok() && $source['required']) {
                        return $this->ResponseError(503, 'Service failed', "{$key} failed");
                    }
                    if (key_exists('data', $response->json())) {
                        $results[$key] = $response->json()['data'];
                    } else {
                        $results[$key] = $response->json();
                    }
                } catch (ConnectionException $e) {
                    return $this->ResponseError(503, 'Source not available', "");
                }

            }
        }
        // *****************---------------------DA FARE ---------- ******////
        $string = view('result', compact('results'))->render();
        return response()->json(json_decode($string));
    }

    public function delete($id)
    {
        $configuration = MetaApiConfiguration::find($id);
        if (!$configuration) {
            return $this->ResponseError(404, 'Not found', 'Configuration not found');
        }

        $configuration->delete();
        return response()->json([], 204);
    }

    public function toggle($id)
    {

        $configuration = MetaApiConfiguration::find($id);

        if (!$configuration) {
            return $this->ResponseError(404, 'Not found', 'Configuration not found');
        }

        if ($configuration->enabled) {
            $configuration->enabled = false;
        } else {
            $configuration->enabled = true;
        }

        $configuration->save();
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
