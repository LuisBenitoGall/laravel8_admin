<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckTimetableTask::class,
        Commands\CompanyAccountsTask::class,
        Commands\EmployeesExpirationTask::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){
        //Production Commands:
        $schedule->command('check_timetable:task')->dailyAt('23:50');        
        //->weekdays()  sólo días laborables.
        $schedule->command('company_accounts:task')->dailyAt('00:01');
        $schedule->command('employees_expiration:task')->dailyAt('00:02');
        
        //Test Commands
        //$schedule->command('check_timetable:task')->everyMinute();      
        //$schedule->command('company_accounts:task')->everyMinute();
        //$schedule->command('employees_expiration:task')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(){
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
