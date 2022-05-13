<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

//Models:
use App\Models\CompanyAccounts;

class CompanyAccountsTask extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company_accounts:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualización de las cuentas de empresa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){
        $today = Carbon::today();

        $accounts = CompanyAccounts::select('id', 'start_date', 'end_date', 'status')
        ->whereIn('status', [0,1])
        ->get();   

        if($accounts->count()){
            foreach($accounts as $row){
                //Desactivar:
                if($row->status == 1 && $row->end_date > $today){
                    $row->status = 2;
                    $row->save();
                }
                //Activar:
                if($row->status == '0' && $row->start_date <= $today){
                    $row->status = 1;
                    $row->save();
                }                
            }
        }
    }
}
