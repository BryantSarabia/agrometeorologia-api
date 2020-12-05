<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{

    public function __construct()
    {
        $this->middleware(['web','dashboard']);
    }

    public function create(){
        return view('admin.pages.configuration_create');
    }

    public function save(){

    }

    public function index(){
        $configurations = MetaApiConfiguration::paginate(5);
        return view('admin.pages.configurations', compact('configurations'));
    }

    public function show(MetaApiConfiguration $configuration){
        return response()->json(json_decode($configuration->configuration));
    }


}
