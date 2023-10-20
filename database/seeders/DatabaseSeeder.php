<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder{
    /**
     * Seed the application's database.
     *
     * Comando para correr un seeder específico: php artisan db:seed --class=NameSeeder
     *
     * @return void
     */
    public function run(){
        //Mantener el orden de ejecución de los seeds:
        $this->call(AppSettingsTableSeeder::class);
        $this->call(AccountingAccountTypesTableSeeder::class);  //php artisan db:seed --class=AccountingAccountTypesTableSeeder
        $this->call(AccountsTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
    	$this->call(ProvincesTableSeeder::class);
    	$this->call(TownsTableSeeder::class);
        //$this->call(PostalCodesTableSeeder::class);
        $this->call(ModulesTableSeeder::class);
        $this->call(FunctionalitiesTableSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UserCompaniesTableSeeder::class);
        $this->call(StockMovementsTableSeeder::class);
        //$this->call(BudgetFormItemsTableSeeder::class);

        //FACTORIES:
        //\App\Models\User::factory(100)->create();
        //\App\Models\Product::factory(200)->create();
        //\App\Models\Group::factory(50000)->create();
    }
}
