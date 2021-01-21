<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Yaml;

class ConfigurationController extends Controller
{
    use ResponsesJSON, UtilityMethods;

    public function __construct()
    {
        $this->middleware(['web', 'dashboard']);
    }

    public function create()
    {
        return view('admin.pages.configuration_create');
    }

    public function save(Request $request)
    {
        $validator = $request->validate([
            'configuration_file' => 'required|max:4096'
        ]);

        $file = $request->file('configuration_file');
        if ($file->getClientMimeType() !== 'application/json') {
            return redirect()->back()->withErrors(['file_error' => 'The file must be a JSON file']);
        }

        $file_content = file_get_contents($file);
        $conf = json_decode($file_content, true);
        if (!is_array($conf)) {
            return redirect()->back()->withErrors(['parsing_error' => 'Parsing error, control the file']);
        }
        if (!key_exists('group', $conf)) {
            return redirect()->back()->withErrors(['group' => 'Missing group']);
        } elseif (!key_exists('service', $conf)) {
            return redirect()->back()->withErrors(['service' => 'Missing service']);
        } elseif (!key_exists('operations', $conf)) {
            return redirect()->back()->withErrors(['operations' => 'Missing operations']);
        }

        // Controllo le proprieta di ogni source
        foreach ($conf['operations'] as $key => $operation) {
            if (!key_exists('sources', $operation) || !key_exists('result', $operation)) {
                return redirect()->back()->withErrors(['souces' => "Missing sources at {$key}"]);
            }

            if(!key_exists('description', $operation)){
                return redirect()->back()->withErrors(['description' => "Description missing at {$key}"]);
            }

            if (!is_string($operation['description'])) {
                return redirect()->back()->withErrors(['description' => "Description must be a string at {$key}"]);
            }

            if (key_exists('params', $operation)) {
                foreach ($operation['params'] as $key => $param) {
                    if (!key_exists('required', $param)) {
                        return redirect()->back()->withErrors(['required' => "Missing required parameter at {$key}"]);
                    }
                    if (!$this->validateType("boolean", $param['required'])) {
                        return redirect()->back()->withErrors(['required' => "Required must be a boolean at {$key}"]);
                    }
                    if($param['required'] === false && !key_exists('default', $param)){
                        return redirect()->back()->withErrors(['default' => "Default parameter is missing at {$key}"]);
                    }
                    if (!key_exists('type', $param)) {
                        return redirect()->back()->withErrors(['type' => "Missing type parameter at {$key}"]);
                    }
                    if (key_exists('description', $param)) {
                        if (!$this->validateType("string", $param['description'])) {
                            return redirect()->back()->withErrors(['description' => "Description parameter must be a string at {$key}"]);
                        }
                    }

                    if (!$this->validateType("boolean", $param['required'])) {
                        return redirect()->back()->withErrors(['required' => "Required parameter must be a boolean at {$key}"]);
                    }

                    if (!$this->validateType("string", $param['type'])) {
                        return redirect()->back()->withErrors(['required' => "Type parameter must be a string at {$key}"]);
                    }
                }
            }

            foreach ($operation['sources'] as $key => $source) {

                if (!is_bool($source['required'])) {
                    return redirect()->back()->withErrors(['required' => "Required must be a boolean at {$key}"]);
                }

                if (!key_exists('method', $source)) {
                    return redirect()->back()->withErrors(['method' => "Missing method at {$key} source"]);
                }

                if (strtoupper($source['method']) !== "GET" && strtoupper($source['method']) !== "POST") {
                    return redirect()->back()->withErrors(['method' => "Method not supported at {$key} source"]);
                }

                if (strtoupper($source['method']) === "POST") {
                    if (!key_exists("payloadType", $source)) {
                        return redirect()->back()->withErrors(['payloadType' => "Missing payload type at at {$key} source"]);
                    }
                    if (!key_exists("payloadTemplate", $source)) {
                        return redirect()->back()->withErrors(['payloadTemplate' => "Missing payload template at {$key} source"]);
                    }
                }
            }
        }

        $group = $conf['group'];
        $service = $conf['service'];
        $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
        if ($obj) {
            return redirect()->back()->withErrors(['conf_exists' => 'This configuration already exists']);
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

                if (strtoupper($source['method']) === "POST") {
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

        $configuration = json_decode($obj->configuration, true);
        $template = view('metaAPI.generate_specification', compact('configuration'))->render();
        $decoded_template = json_decode($template, true);
        if ($decoded_template === null) {
            return redirect()->back()->withErrors(['parsing_error' => 'Parsing error, control the file']);
        }
        Storage::disk('public')->put($group . "-" . $service . ".json", $template);
//
//        try{
//            $yaml = Yaml::dump($decoded_template, 9, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
//        } catch(DumpException $e){
//            return $this->ResponseError(500, "Internal server error", "Yaml parsing error");
//        }
//        $path = resource_path('views') . "\\metaAPI" . "\\configurations\\" . $group . "\\" . $service . "\\specification";
//        if(!is_dir($path)) {
//            mkdir($path, 0777, true);
//        }
//        $file = fopen($path . "\\specification.yaml", "w");
//        fwrite($file, $yaml);
//        fclose($file);

        return redirect()->route('admin.configuration.all')->with('message', 'Configuration created');

    }

    public function index()
    {
        $configurations = MetaApiConfiguration::paginate(5);
        return view('admin.pages.configurations', compact('configurations'));
    }

    public function show($id)
    {
        $configuration = MetaApiConfiguration::find($id);
        if (!$configuration) {
            return redirect()->route('admin.configuration.all')->withErrors(['error' => 'Configuration not found']);
        }

        return response()->json(json_decode($configuration->configuration));
    }

    public function download_specification($id)
    {
        $configuration = MetaApiConfiguration::find($id);
        if (!$configuration) {
            return redirect()->route('admin.configuration.all')->withErrors(['error' => 'Configuration not found']);
        }
        $group_and_service = json_decode($configuration->configuration);
        $specification_name = $group_and_service->group . "-" . $group_and_service->service . ".json";

        if (Storage::disk('public')->exists($specification_name)) {
            return Storage::disk('public')->download($specification_name, $specification_name, [
                'Content-Type' => 'application/json'
            ]);
        }
    }


}
