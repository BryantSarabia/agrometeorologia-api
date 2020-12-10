<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use App\Traits\ResponsesJSON;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;

class ConfigurationController extends Controller
{
    use ResponsesJSON;

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
            if (!key_exists('sources', $conf['operations'][$key]) || !key_exists('result', $conf['operations'][$key])) {
                return redirect()->back()->withErrors(['parameters' => "Missing parameters at {$key}"]);
            }
            foreach ($operation['sources'] as $key => $source) {
                if (!filter_var($source['urlTemplate'], FILTER_VALIDATE_URL)) {
                    return redirect()->back()->withErrors(['source_url' => "Invalid url source at {$key}"]);
                }

                if (!is_string($source['description'])) {
                    return redirect()->back()->withErrors(['description' => 'Description must be a string']);
                } elseif (!filter_var($source['required'], FILTER_VALIDATE_BOOL)) {
                    return redirect()->back()->withErrors(['required' => 'Required must be a boolean']);
                }
            }

        }
        $group = $conf['group'];
        $service = $conf['service'];
        $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
        if ($obj) {
            return redirect()->back()->withErrors(['conf_exists' => 'This configuration already exists']);
        }

        $obj = MetaApiConfiguration::create([
            'configuration' => json_encode($conf),
        ]);
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


}
