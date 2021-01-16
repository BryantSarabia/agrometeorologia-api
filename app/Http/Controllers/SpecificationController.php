<?php

namespace App\Http\Controllers;

use App\Models\MetaApiConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpecificationController extends Controller
{
    public function show($id = null)
    {
        if($id){
        $configuration = MetaApiConfiguration::find($id);
        if (!$configuration) {
            return redirect()->route('home');
        }
        $group_and_service = json_decode($configuration->configuration);
        $path = $group_and_service->group . "-" . $group_and_service->service . ".json";
        $url = Storage::url($path);
        return view('swagger.specification', compact('url'));
        } else {
            return view('swagger.specification');
        }
    }

    public function index(){
        $configurations = MetaApiConfiguration::paginate(5);
        return view('pages.specifications', compact('configurations'));
    }
}
