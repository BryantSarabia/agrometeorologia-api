<?php

namespace App\Http\Controllers\MetaAPI;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
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
            $conf = $request->all();

        } else {
            return $this->ResponseError(400, 'Bad request', 'Missing configuration');
        }


        if (!is_array($conf)) {
            return $this->ResponseError(400, 'Bad request', 'Parsing error');
        }

        // Controllo se esistono le proprietà nelle configurazioni

        if (!key_exists('group', $conf)) {
            return $this->ResponseError(400, 'Bad request', 'Missing group');
        } elseif (!key_exists('service', $conf)) {
            return $this->ResponseError(400, 'Bad request', 'Missing service');
        } elseif (!key_exists('operations', $conf)) {
            return $this->ResponseError(400, 'Bad request', 'Missing operations');
        }

        // Controllo le proprieta di ogni source
        foreach ($conf['operations'] as $key => $operation) {
            if (!key_exists('sources', $operation) || !key_exists('result', $operation)) {
                return $this->ResponseError(400, 'Bad request', "Missing parameters at {$key}");
            }

            if (key_exists('params', $operation)) {
                foreach ($operation['params'] as $key => $param) {
                    if (!key_exists('required', $param)) {
                        return $this->ResponseError(400, 'Bad request', "Missing required parameter at {$key}");
                    }

                    if (!$this->validateType("boolean", $param['required'])) {
                        return $this->ResponseError(400, 'Bad request', "Required must be a boolean at {$key}");
                    }

                    if (!key_exists('type', $param)) {
                        return $this->ResponseError(400, 'Bad request', "Missing type parameter at {$key}");
                    }

                    if (key_exists('description', $param)) {
                        if (!$this->validateType("string", $param['description'])) {
                            return $this->ResponseError(400, 'Bad request', "Description parameter must be a string at {$key}");
                        }
                    }

                    if (!$this->validateType("boolean", $param['required'])) {
                        return $this->ResponseError(400, 'Bad request', "Required paramter must be a boolean at {$key}");
                    }

                    if (!$this->validateType("string", $param['type'])) {
                        return $this->ResponseError(400, 'Bad request', "Type parameter must be a string at {$key}");
                    }
                }
            }
            foreach ($operation['sources'] as $key => $source) {

                if (!is_string($source['description'])) {
                    return $this->ResponseError(400, 'Bad request', "Description must be a string at {$key}");
                }

                if (!is_bool($source['required'])) {
                    return $this->ResponseError(400, 'Bad request', "Required must be a boolean at {$key}");
                }

                if (!key_exists('method', $source)) {
                    return $this->ResponseError(400, 'Bad request', "Missing method at {$key} source");
                }

                if ($source['method'] !== "GET" && $source['method'] !== "POST") {
                    return $this->ResponseError(400, 'Bad request', "Method not supported at {$key} source");
                }

                if (!key_exists("payloadType", $source)) {
                    return $this->ResponseError(400, 'Bad request', "Missing payload type at {$key} source");
                }

                if (!key_exists("payloadTemplate", $source)) {
                    return $this->ResponseError(400, 'Bad request', "Missing payload template at {$key} source");
                }

            }
        }
        $group = $conf['group'];
        $service = $conf['service'];
        $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
        if ($obj) {
            return $this->ResponseError(400, 'Bad request', 'This configuration already exists');
        }

        foreach ($conf['operations'] as $operation_key => $operation) {
            foreach ($operation['sources'] as $source_key => $source) {
                $path = resource_path('views') . "\\metaAPI" . "\\configurations\\" . $group . "\\" . $service . "\\" . $operation_key;
                if (!is_dir($path . "\\sources")) {
                    mkdir($path . "\\sources", 0777, true);
                }
                $file = fopen($path . "\\sources\\" . $source_key . ".blade.php", 'w');
                fwrite($file, $source['urlTemplate']);
                fclose($file);

                if ($source['method'] === "POST") {
                    $file = fopen($path . "\\sources\\" . $source_key . "-payload.blade.php", 'w');
                    fwrite($file, $source['payloadTemplate']);
                    fclose($file);
                }
            }
            if (!is_dir($path . "\\result")) {
                mkdir($path . "\\result", 0777, true);
            }
            $file = fopen($path . "\\result\\" . "template.blade.php", 'w');
            fwrite($file, $operation['result']['template']);
            fclose($file);

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
        } elseif (!$obj->enabled) {
            return $this->ResponseError(503, 'Service Unavailable', 'This configuration is disabled');
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
                    $data[$key] = $query_params[$key];
                }
            }
        }
        // process source
        $sources = $conf['operations'][$operation]['sources'];
        $results = [];


        if (count($sources) > 1) {
            foreach ($sources as $key => $source) {
                if (isset($data)) {
                    $url = view("metaAPI.configurations.{$group}.{$service}.{$operation}.sources.{$key}", $data)->render();
                } else {
                    $url = view("metaAPI.configurations.{$group}.{$service}.{$operation}.sources.{$key}")->render();
                }
                if (!filter_var($url, FILTER_VALIDATE_URL)) { // mi assicuro che la url valutata sia sempre una url valida
                    return $this->ResponseError(500, 'Internal server error', "The URL generated is not valid");
                }
                if ($source['method'] === "GET") {
                    try {
                        $response = Http::timeout(10)->get($url);
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
                } elseif ($source['method'] === "POST") {
                    $body = view("metaAPI.configurations.{$group}.{$service}.{$operation}.sources.{$key}-payload", $data)->render();
                    $body = json_decode($body);
                    try {
                        $response = Http::timeout(10)->withHeaders(['Content-Type' => $source['payloadType']])->post($url, $body);
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

        } else {

            $source = key($sources);
            if (isset($data)) {
                $url = view("metaAPI.configurations.{$group}.{$service}.{$operation}.sources.{$source}", $data)->render();
            } else {
                $url = view("metaAPI.configurations.{$group}.{$service}.{$operation}.sources.{$source}")->render();
            }
            if (!filter_var($url, FILTER_VALIDATE_URL)) { // mi assicuro che la url valutata sia sempre una url valida
                return $this->ResponseError(500, 'Internal server error', "The URL generated is not valid");
            }
            if ($source['method'] === "GET") {
                try {
                    $response = Http::timeout(10)->get($url);
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
            } elseif ($source['method'] === "POST") {
                $body = view("metaAPI.configurations.{$group}.{$service}.{$operation}.sources.{$source}-payload", $data)->render();
                $body = json_decode($body);
                try {
                    $response = Http::timeout(10)->withHeaders(['Content-Type' => $source['payloadType']])->post($url, $body);
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
        $string = view("metaAPI.configurations.{$group}.{$service}.{$operation}.result.template", compact('results'))->render();
        $json = json_decode($string);
        if ($json === null) {
            return $this->ResponseError(500, "Internal server error", "Parsing error");
        }
        return response()->json($json);
    }

    public function delete($id)
    {
        $configuration = MetaApiConfiguration::find($id);
        if (!$configuration) {
            return $this->ResponseError(404, 'Not found', 'Configuration not found');
        }
        $obj = json_decode($configuration->configuration);
        $dir = resource_path('views') . DIRECTORY_SEPARATOR . "metaAPI\\configurations" . DIRECTORY_SEPARATOR . $obj->group . DIRECTORY_SEPARATOR . $obj->service;
        $this->rrmdir($dir);
        if ($this->is_dir_empty(resource_path('views') . DIRECTORY_SEPARATOR . "metaAPI\\configurations" . DIRECTORY_SEPARATOR . $obj->group)) {
            rmdir(resource_path('views') . DIRECTORY_SEPARATOR . "metaAPI\\configurations" . DIRECTORY_SEPARATOR . $obj->group);
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
                case 'date':
                    $date = explode('-', $value);
                    return checkdate($date[1], $date[2], $date[0]);
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
