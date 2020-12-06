<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use App\Traits\ResponsesJSON;
use App\Traits\UtilityMethods;
use Illuminate\Http\Request;

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

//    public function save(Request $request)
//    {
//        $validator = $request->validate([
//            'configuration_file' => 'required|max:4096'
//        ]);
//
//        $file = $request->file('configuration_file');
//        if ($file->getClientMimeType() !== 'application/json') {
//            return redirect()->back()->withErrors(['file_error' => 'The file must be a JSON file']);
//        }
//
//        $file_content = file_get_contents($file);
//        $conf = json_decode($file_content, true);
//        if (!is_array($conf)) {
//            return redirect()->back()->withErrors(['parsing_error' => 'Parsing error, control the file']);
//        }
//
//        if ($this->validateConfiguration($conf, false)) {
//            $obj = MetaApiConfiguration::create([
//                'configuration' => json_encode($conf),
//            ]);
//            return redirect()->route('admin.configuration.all')->with('message','Configuration created');
//        } else {
//            return $this->ResponseError(500, 'Internal server error', 'An error occurred');
//        }
//
//
//    }

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

    public function delete($id)
    {
        $configuration = MetaApiConfiguration::find($id);
        if (!$configuration) {
            return $this->ResponseError(404, 'Not found', 'Configuration not found');
        }

        $configuration->delete();
        return response()->json([], 204);
    }


}
