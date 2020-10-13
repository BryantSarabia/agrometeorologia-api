<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $user = Auth::user();
        $projects = Project::where('user_id',$user->id)->simplePaginate(5);
        return view('pages.projects',['projects' => $projects]);
    }

    public function create(){
        return view('pages.create_project');
    }

    public function save(Request $request){

        $validator = $request->validate([
            'name' => 'bail|string|required'
        ]);

        $user = Auth::user();

        $project = new Project;
        $project->name = $request->name;

        $user->projects()->save($project);

        return redirect()->route('project.index')->with(['created' => 'Project created with success']);

    }

    public function delete(Project $project){

        $user = Auth::user();
        if($project->user->id !== $user->id){
            abort(401);
        }
        $project->delete();
        return response()->json([],204);
    }

    /* Method to generate api key */
    public function token(Request $request,Project $project){

        $user = Auth::user();

        if($project->user->id !== $user->id || !Hash::check($request->password,$user->password)){
            return response()->json([
                'message' => 'Password incorrect'
            ],401);
        }

        if($request->generate){
            do{
                $api_key = str::random(30);
                $check = Project::where('api_key',$api_key)->first();
            }while($check);

            $project->api_key = $api_key;
            $project->save();

            return response()->json([
                'api_key' => $api_key
            ]);
        } else {
            return response()->json([
                'api_key' => $project->api_key
            ]);
        }


    }
}
