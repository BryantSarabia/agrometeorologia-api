<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(){
        return ProjectCollection::make(Project::with(['user','requests'])->get());
    }

    public function show(Project $project){
        return ProjectResource::make($project->load(['user','requests']));
    }


}
