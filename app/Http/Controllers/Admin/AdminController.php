<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaApiConfiguration;
use App\Models\Project;
use App\Models\User;
use App\Models\Request as APIRequest;
use App\Traits\ResponsesJSON;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ResponsesJSON;

    public function __construct()
    {
        $this->middleware(['web', 'dashboard']);
    }

    public function home()
    {
        $users = User::all()->count();
        $projects = Project::all()->count();
        $configurations = MetaApiConfiguration::all()->count();
        return view('admin.home', compact('users', 'projects', 'configurations'));
    }

    public function users(){
        $users =  User::paginate(5);
        return view('admin.pages.users', compact('users'));
    }

    public function userDelete($id){
        $user = User::find($id);
        if(!$user){
            return $this->ResponseError(404, 'Not found', 'User not found');
        }
        if($user->type === 'admin'){
            return $this->ResponseError(400, 'Bad request', 'Administrators cannot be deleted');
        }
        $user->delete();
        return response()->json([],204);
    }

    public function projectDelete($id){
        $project = Project::find($id);
        if(!$project){
            return $this->ResponseError(404, 'Not found', 'Project not found');
        }
        $project->delete();
        return response()->json([],204);
    }

    public function projects(){
        $projects = Project::paginate(5);
        return view('admin.pages.projects', compact('projects'));
    }

    public function analytics()
    {
        $requests = APIRequest::where('date', '>', date('Y-01-01'))->where('date', '<', date('Y-12-31'))->get()->sortBy('date');
        $total_requests = $requests->mapToGroups(function ($item, $key) {
            return [date('F', strtotime($item->date)) => $item];
        });
        $total_requests->each(function ($request, $key) use ($total_requests){
            $total_requests[$key] = $request->reduce(function ($carry, $item){
                return $carry + $item->number;
            },0);
        });

        $projects = Project::with('requests')->get();
        $projects->each(function($project){
           $project->total = $project->requests->sum('number');
        });

        $users = User::with('requests')->get();
        $users->each(function($user){
            $user->total = $user->requests->sum('number');
        });

        $endpoints = $requests->groupBy('endpoint');
        $endpoints->each(function ($endpoint, $key) use ($endpoints){
            $endpoints[$key] = $endpoint->reduce(function ($carry, $item){
                return $carry + $item->number;
            },0);
        });
        return response()->json([
            'total_usage' => $total_requests,
            'projects' => $projects,
            'users' => $users,
            'endpoints' => $endpoints,
        ]);
    }
}
