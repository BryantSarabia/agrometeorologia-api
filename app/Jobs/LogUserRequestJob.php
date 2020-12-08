<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogUserRequestJob implements ShouldQueue
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
        $user = User::where('token', $this->token)->first();
        $date = date('Y-m-d');
        $log = $user->requests->where('endpoint', $this->path)->where('date', $date)->first();
        if ($log) {
            $log->number += 1;
            $log->save();
        } else {
            $user->requests()->create([
                'number' => 1,
                'date' => $date,
                'endpoint' => $this->path
            ]);
        }
    }
}
