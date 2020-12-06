<?php

namespace App\Traits;

use App\Models\MetaApiConfiguration;
use DateTime;

trait UtilityMethods
{

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function validateID($id)
    {
        return preg_match('/[^0-9]/', $id);
    }

    public function validateCoordinate($coordinate)
    {
        return filter_var($coordinate, FILTER_VALIDATE_FLOAT);
    }

    public function validateString($string)
    {
        return !preg_match('/[^A-Za-z]+/', $string);
    }

//    public function validateConfiguration($conf, $ajax)
//    {
//        if ($ajax) {
//            if (!key_exists('group', $conf)) {
//                return $this->ResponseError(400, 'Bad request', 'Missing group');
//            } elseif (!key_exists('service', $conf)) {
//                return $this->ResponseError(400, 'Bad request', 'Missing service');
//            } elseif (!key_exists('operations', $conf)) {
//                return $this->ResponseError(400, 'Bad request', 'Missing operations');
//            }
//
//            // Controllo le proprieta di ogni source
//            foreach ($conf['operations'] as $key => $operation) {
//                if (!key_exists('sources', $conf['operations'][$key]) || !key_exists('result', $conf['operations'][$key])) {
//                    return $this->ResponseError(400, 'Bad request', "Missing parameters at {$key}");
//                }
//                foreach ($operation['sources'] as $key => $source) {
//                    if (!filter_var($source['urlTemplate'], FILTER_VALIDATE_URL)) {
//                        return $this->ResponseError(400, 'Bad request', "Invalid source url at {$key}");
//                    }
//
//                    if (!is_string($source['description'])) {
//                        return $this->ResponseError(400, 'Bad request', "Description must be a string at {$key}");
//                    } elseif (!filter_var($source['required'], FILTER_VALIDATE_BOOL)) {
//                        return $this->ResponseError(400, 'Bad request', "required must be a boolean at {$key}");
//                    }
//                }
//            }
//            $group = $conf['group'];
//            $service = $conf['service'];
//
//            $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
//            if ($obj) {
//                return $this->ResponseError(400, 'Bad request', 'This configuration already exists');
//            }
//        } else {
//            if (!key_exists('group', $conf)) {
//                return redirect()->back()->withErrors(['group' => 'Missing group']);
//            } elseif (!key_exists('service', $conf)) {
//                return redirect()->back()->withErrors(['service' => 'Missing service']);
//            } elseif (!key_exists('operations', $conf)) {
//                return redirect()->back()->withErrors(['operations' => 'Missing operations']);
//            }
//
//            // Controllo le proprieta di ogni source
//            foreach ($conf['operations'] as $key => $operation) {
//                if (!key_exists('sources', $conf['operations'][$key]) || !key_exists('result', $conf['operations'][$key])) {
//                    return redirect()->back()->withErrors(['parameters' => "Missing parameters at {$key}"]);
//                }
//                foreach ($operation['sources'] as $key => $source) {
//                    if (!filter_var($source['urlTemplate'], FILTER_VALIDATE_URL)) {
//                        return redirect()->back()->withErrors(['source_url' => "Invalid url source at {$key}"]);
//                    }
//
//                    if (!is_string($source['description'])) {
//                        return redirect()->back()->withErrors(['description' => 'Description must be a string']);
//                    } elseif (!filter_var($source['required'], FILTER_VALIDATE_BOOL)) {
//                        return redirect()->back()->withErrors(['required' => 'Description must be a boolean']);
//                    }
//                }
//
//            }
//            $group = $conf['group'];
//            $service = $conf['service'];
//            $obj = MetaApiConfiguration::where('configuration->group', $group)->where('configuration->service', $service)->first();
//            if ($obj) {
//                return redirect()->back()->withErrors(['conf_exists' => 'This configuration already exists']);
//            }
//            /**  Writing a new route **/
////        $routeFile = fopen(base_path() . '\routes\meta-api.php', "a");
////        if($routeFile){
////            foreach ($operations as $key => $operation) {
////                $newRoute = "Route::get('services/{$group}/{$service}/{$key}','MetaController@get')->name('{$group}.{$service}.{$key}');\n";
////                fwrite($routeFile, $newRoute);
////            }
////            fclose($routeFile);
////        }
//        }
//    }


}
