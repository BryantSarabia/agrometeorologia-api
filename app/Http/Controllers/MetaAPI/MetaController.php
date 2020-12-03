<?php

namespace App\Http\Controllers\MetaAPI;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    public function save(Request $request)
    {

        $conf = $request->configuration;
        if (!key_exists('group', $conf)) {
            return "missing group";
        } elseif (!key_exists('service', $conf)) {
            return "missing service";
        } elseif (!key_exists('operations', $conf)) {
            return "missing operations";
        }

        foreach ($conf['operations'] as $key => $operation) {
            if (!key_exists('sources', $conf['operations'][$key]) || !key_exists('result', $conf['operations'][$key])) {
                return "missing parameters at " . $key;
            }
        }

        $group = $conf['group'];
        $service = $conf['service'];
        $operations = $conf['operations'];

        $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service',$service)->first();
        if($obj){
            return "This conf already exists";
        }

//        $routeFile = fopen(base_path() . '\routes\meta-api.php', "a");
//        foreach ($operations as $key => $operation) {
//            $newRoute = "Route::get('/services/" . $group . '/' . $service . '/' . $key . "','MetaController@get');\n";
//            fwrite($routeFile, $newRoute);
//        }
//        fclose($routeFile);

        $obj = MetaApiConfiguration::create([
           'configuration' => json_encode($conf),
        ]);

    }

    public function get(Request $request, $group, $service, $operation)
    {

    }
}
