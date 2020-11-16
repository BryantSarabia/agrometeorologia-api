<?php

namespace App\Console\Commands;

use App\Mail\PestReports;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReportsEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reports to the user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();

        $users->each(function ($user) {
            if ($user->locations->count() > 0) {
                $reports = collect();
                $user->locations->each(function ($location) use ($reports) {
                    $reports->push($location->findNearestReports($location->lat, $location->lon, $location->radius));
                });
                Mail::to($user)->queue(new PestReports($user, $reports->first()->unique()));
            }
        });
        $this->info('The emails are send successfully!');
    }
}
