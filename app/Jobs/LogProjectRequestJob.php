<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;


class LogProjectRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $path, $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path, $token)
    {
        $this->path = $path;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $project = Project::where('api_key',$this->token)->first();
        $date = date('Y-m-d');
        $log = $project->requests->where('endpoint', $this->path)->where('date', $date)->first();
        if($log){
            $log->number += 1;
            $log->save();
        } else {
          $project->requests()->create([
             'number' => 1,
             'date' => $date,
              'endpoint' => $this->path
          ]);
        }
    }
}
