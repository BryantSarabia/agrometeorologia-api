<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestCollection;
use App\Http\Resources\RequestResource;
use App\Models\Project;
use App\Models\Request as Endpoint;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(){
        return RequestCollection::make(Endpoint::with('project')->get());
    }

    public function show(Endpoint $request){
        return RequestResource::make($request->load('project'));
    }

    public function requests(Project $project){
        return RequestCollection::make($project->requests->load('project'));
    }
}
