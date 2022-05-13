<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

//Models:
use App\Models\AppSettings;

class AppSettingsTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        //Default settings:
        $data = [
            ['field' => 'initial_imports',
            'value' => false]
        ];

        foreach ($data as $key => $value){
            AppSettings::create($value);
        }   
    }
}
