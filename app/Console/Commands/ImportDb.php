<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Attribute;
use App\Models\Batch;
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\BudgetPatterns;
use App\Models\BudgetSettings;
use App\Models\BusinessAreas;
use App\Models\BusinessAreaSettings;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\Contract;
use App\Models\CostCenters;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CustomerProducts;
use App\Models\CustomerProviders;
use App\Models\DayTypes;
use App\Models\DayTypesConfig;
use App\Models\DayTypesUsersConfig;
use App\Models\Employee;
use App\Models\Group;
use App\Models\GroupMembers;
use App\Models\Importaciones;
use App\Models\Incidence;
use App\Models\IncidenceLines;
use App\Models\IncidencePatterns;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\InvoicePatterns;
use App\Models\InvoiceSettings;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Module;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDocs;
use App\Models\OrderPatterns;
use App\Models\OrderPrevisions;
use App\Models\OrderSettings;
use App\Models\OrderTracking;
use App\Models\Phone;
use App\Models\Preference;
use App\Models\Product;
use App\Models\ProductAttributes;
use App\Models\ProductDocs;
use App\Models\ProductPatterns;
use App\Models\ProductPrices;
use App\Models\ProductSerials;
use App\Models\ProductSerialsCards;
use App\Models\Project;
use App\Models\Province;
use App\Models\PurchasePatterns;
use App\Models\ResiduaryDays;
use App\Models\Schedule;
use App\Models\ScheduleEvents;
use App\Models\ScheduleEventTypes;
use App\Models\Store;
use App\Models\Task;
use App\Models\TaskDocs;
use App\Models\TaskMessages;
use App\Models\TaskStatus;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\TimetableSigned;
use App\Models\Town;
use App\Models\User;
use App\Models\UserBusinessAreas;
use App\Models\UserCompanies;
use App\Models\UserCostCenters;
use App\Models\UserImage;
use App\Models\UserTimetables;
use App\Models\UserWorkplaces;
use App\Models\WorkCalendar;
use App\Models\Workplace;
use App\Models\WorkUserCalendar;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ImportDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return void|null
     */
    public function handle()
    {
        Currency::import();
        $this->output->progressStart(0);
        DB::table('importaciones')->truncate();
        $this->importUsers(true);                   // 2.
        $this->info('Importando usuarios');
        $this->output->progressAdvance();
        $this->importCompanies();                   // 3.
        $this->info('Importando Compañias');
        $this->output->progressAdvance();
        $this->importCountries();                   // 4.
        $this->info('Importando Countries');
        $this->output->progressAdvance();
        $this->importProvinces();                   // 5.
        $this->info('Importando Provincias');
        $this->output->progressAdvance();
        $this->importTowns();                       // 6.
        $this->info('Importando Towns');
        $this->output->progressAdvance();
        $this->importWorkplaces();                  // 7.
        $this->info('Importando WorkPlaces');
        $this->output->progressAdvance();
        $this->importCostCenters();                 // 8.
        $this->info('Importando Centers');
        $this->output->progressAdvance();
        $this->importBusinessAreas();               // 9.
        $this->info('Importando Business Areas');
        $this->output->progressAdvance();
        $this->importUserCompanies();               // 10.
        $this->info('Importando User Companies');
        $this->output->progressAdvance();
        $this->importContracts();                   // 11.
        $this->info('Importando Contratos');
        $this->output->progressAdvance();
        $this->importDayTypes();                    // 12.
        $this->info('Importando Day Types');
        $this->output->progressAdvance();
        $this->importDayTypesSettings();            // 13.
        $this->info('Importando Day Type Settings');
        $this->output->progressAdvance();
        $this->importDayTypesSettingsUser();        // 14.
        $this->info('Importando  Day Type Setting User');
        $this->output->progressAdvance();
        $this->importUserWorkplaces();              // 15.
        $this->info('Importando User Work Places');
        $this->output->progressAdvance();
        $this->importUserCostCenters();             // 16.
        $this->info('Importando User Cost Centers');
        $this->output->progressAdvance();
        $this->importUserBusinessAreas();           // 17.
        $this->info('Importando User Bussines Areas');
        $this->output->progressAdvance();
        $this->importBusinessAreasSettings();       // 18.
        $this->info('Importando Business Areas Settings');
        $this->output->progressAdvance();
        $this->importEmployees();                   // 19.          **¡ATENCIÓN!
        $this->info('Importando Employees');
        $this->output->progressAdvance();
        $this->importAccounts();                    // 20.
        $this->info('Importando Accounts');
        $this->output->progressAdvance();
        $this->importCompanyAccounts();             // 21.
        $this->info('Importando Company Accounts');
        $this->output->progressAdvance();
        $this->importUserImages();                  // 22.
        $this->info('Importando User Images');
        $this->output->progressAdvance();
        $this->importPhones();                      // 23.
        $this->info('Importando Phones');
        $this->output->progressAdvance();
        $this->importCategories();                   // 24.
        $this->info('Importando Categoories');
        $this->output->progressAdvance();
        $this->importCompanyModules();              // 25.
        $this->info('Importando Company Modules');
        $this->output->progressAdvance();
        $this->importCompanySettings();             // 26.
        $this->info('Importando Company Settings');
        $this->output->progressAdvance();
        $this->importGroups();                      // 27.
        $this->info('Importando Groups');
        $this->output->progressAdvance();
        $this->importGroupMembers();                // 28.
        $this->info('Importando Group Members');
        $this->output->progressAdvance();
        $this->importNotifications();               // 29.
        $this->info('Importando Notifications');
        $this->output->progressAdvance();
        $this->importPreferences();                 // 30.
        $this->info('Importando Preferences');
        $this->output->progressAdvance();
        $this->importStores();                      // 31.
        $this->info('Importando Stores');
        $this->output->progressAdvance();
        $this->importProductPatterns();             // 32.
        $this->info('Importando Product Patterns');
        $this->output->progressAdvance();
        $this->importTimetableItems();              // 33.          **¡ATENCIÓN!
        $this->info('Importando Time Table Items ***Atención***');
        $this->output->progressAdvance();
        $this->importTimetableSigned();             // 34.
        $this->info('Importando Time Table Signed');
        $this->output->progressAdvance();
        $this->importTimetables();                  // 35.
        $this->info('Importando Time Tables');
        $this->output->progressAdvance();
        $this->importUserTimetables();              // 36.
        $this->info('Importando User Time Table');
        $this->output->progressAdvance();
        $this->importWorkCalendars();               // 37.
        $this->info('Importando Work Calendars');
        $this->output->progressAdvance();
        $this->importWorkUserCalendars();           // 38.
        $this->info('Importando Work User Calendars');
        $this->output->progressAdvance();
        $this->importResiduaryDays();               // 39.
        $this->info('Importando ResiduaryDays');
        $this->output->progressAdvance();
        $this->importBudgetForms();                 // 40.
        $this->info('Importando Budget Forms');
        $this->output->progressAdvance();
        $this->importProducts();                    // 41.
        $this->info('Importando Product');
        $this->output->progressAdvance();
        $this->importBudgetPatterns();              // 42.
        $this->info('Importando Budget Patterns');
        $this->output->progressAdvance();
        $this->importBudgetSettings();              // 43.
        $this->info('Importando Budget Settings');
        $this->output->progressAdvance();
        $this->importOrderPatterns();               // 44.
        $this->info('Importando Order Patterns');
        $this->output->progressAdvance();
        $this->importOrderSettings();               // 45.
        $this->info('Importando Order Settings');
        $this->output->progressAdvance();
        $this->importPurchasePatterns();            // 46.
        $this->info('Importando Purchase Patterns');
        $this->output->progressAdvance();
        $this->importOrders();                      // 47.
        $this->info('Importando Orders');
        $this->output->progressAdvance();
        $this->importCustomerProviders();           // 48.
        $this->info('Importando Customer Providers');
        $this->output->progressAdvance();
        $this->importOrderDocs();                   // 49.
        $this->info('Importando Order Docs');
        $this->output->progressAdvance();
        $this->importCustomerProducts();            // 50.
        $this->info('Importando Customer Products');
        $this->output->progressAdvance();
        $this->importIncidencePatterns();           // 51.
        $this->info('Importando Incidence Patterns');
        $this->output->progressAdvance();
        $this->importAttributes();                  // 53.
        $this->info('Importando Attributes');
        $this->output->progressAdvance();
        $this->importProductAttributes();           // 54.
        $this->info('Importando Product Attribute');
        $this->output->progressAdvance();
        $this->importProductSerials();              // 55.
        $this->info('Product Serials');
        $this->output->progressAdvance();
        $this->importProductSerialsCards();         // 56.
        $this->info('Importando Product Serials Cards');
        $this->output->progressAdvance();
        //$this->importIncidenceLines();              // 57.
        //$this->info('Importando Incidence Lines');
        $this->output->progressAdvance();
        $this->importProductDocs();                 // 58.
        $this->info('Importando Product Docs');
        $this->output->progressAdvance();
        $this->importProductPrices();               // 59.
        $this->info('Importando Product Prices');
        $this->output->progressAdvance();
        $this->importOrderTrackings();              // 60.
        $this->info('Importando Order Trackings');
        $this->output->progressAdvance();
        $this->importBudgetFormItems();             // 61.
        $this->info('Importando Budget Form Items');
        $this->output->progressAdvance();
        $this->importSchedules();                   // 62.
        $this->info('Importando Schedules');
        $this->output->progressAdvance();
        $this->importScheduleEventTypes();          // 63.
        $this->info('Importando Schedule Event Types');
        $this->output->progressAdvance();
        $this->importScheduleEvents();              // 64.
        $this->info('Importando Schedule Events');
        $this->output->progressAdvance();
        $this->importBatches();                     // 65.
        $this->info('Importando Batches');
        $this->output->progressAdvance();
        $this->importInvoicePatterns();             // 66.
        $this->info('Importando Invoices Patterns');
        $this->output->progressAdvance();
        $this->importInvoiceSettings();             // 67.
        $this->info('Importando Invoice Settings');
        $this->output->progressAdvance();
        $this->importInvoices();                    // 68.
        $this->info('Importando Invoices');
        $this->output->progressAdvance();
        $this->importOrderPrevisions();             // 69.
        $this->info('Importando Order Previsions');
        $this->output->progressAdvance();
        $this->importItems();                       // 70.
        $this->info('Importando Items');
        $this->output->progressAdvance();
        $this->importItemsStatus();                 // 71.
        $this->info('Importando Item Status');
        $this->output->progressAdvance();
        $this->importProjects();                    // 72.
        $this->info('Importando Projects');
        $this->output->progressAdvance();
        $this->importTaskStatus();                  // 73.
        $this->info('Importando Task Status');
        $this->output->progressAdvance();
        $this->importTasks();                       // 74.
        $this->info('Importando Tasks');
        $this->output->progressAdvance();
        $this->importTaskDocs();                    // 75.
        $this->info('Importando Task Docs');
        $this->output->progressAdvance();
        $this->importTaskMessages();                // 76.
        $this->info('Importando Task Messages');
        $this->output->progressAdvance();
        $this->importInvoiceItems();                // 77.
        $this->info('Importando Invoice Items');
        $this->output->progressAdvance();
        $this->endImportation();
        $this->info('Finalizando Importanción');
        $this->output->progressFinish();
        return $this->info('La importación se ha realizado con exíto.');
    }


    /**
     * 1.2. Mensajes sobre la importación.
     */
    public function messages($prev = false)
    {
        $next = Importaciones::select('texto')
            ->orderBy('id', 'DESC')
            ->first();

        if ($prev && $next && $next->texto != $prev) {
            return response()->json($next->texto);
        } else {
            return response()->json(false);
        }
    }

    /**
     * 2. Importación usuarios.
     */
    public function importUsers($on = false)
    {
        if ($on) {
            $data = DB::connection('mysql2')->table('users')->get();

            if ($data->count()) {
                Importaciones::create(['texto' => 'usuarios']);

                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('users')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                foreach ($data as $r) {

                    $i = new User;
                    $i->id = $r->id;
                    $i->name = $r->name;
                    $i->surname = $r->surname;
                    $i->email = $r->email;
                    $i->email_verified_at = $r->email_verified_at;
                    $i->password = $r->password;
                    $i->nickname = $r->nickname;
                    $i->birthday = $r->birthday;
                    $i->nif = $r->nif;
                    $i->naf = $r->naf;
                    $i->signature = $r->signature;
                    $i->isAdmin = $r->isAdmin;
                    $i->status = $r->status;
                    $i->remember_token = $r->remember_token;
                    $i->deleted_at = $r->deleted_at;
                    $i->created_at = $r->created_at;
                    $i->updated_at = $r->updated_at;
                    $i->save();
                    // dd($i);
                    //Guardamos rol:
                    //$i->assignRole('basico');
                }
            }
        }
    }

    /**
     * 3. Importación empresas.
     */
    public function importCompanies()
    {
        $data = DB::connection('mysql2')->table('companies')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'empresas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('companies')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Company;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->tradename = $r->tradename;
                $i->acronym = $r->acronym;
                $i->logo = $r->logo;
                $i->nif = $r->nif;
                $i->ccc = $r->ccc;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
                $i->setCompanyRoles();
            }

        }

    }

    /**
     * 4. Importación países.
     */
    public function importCountries()
    {
        $data = DB::connection('mysql2')->table('countries')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'países']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('countries')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Country;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->code = $r->code;
                $i->alfa2 = $r->alfa2;
                $i->alfa3 = $r->alfa3;
                $i->flag = $r->flag;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 5. Importación provincias.
     */
    public function importProvinces()
    {
        $data = DB::connection('mysql2')->table('provinces')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'provincias']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('provinces')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Province;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->country_id = $r->country_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 6. Importar poblaciones.
     * Omitir las poblaciones de España, pues se carga con el seeder.
     */
    public function importTowns()
    {
        // $data = DB::connection('mysql2')->table('towns')
        // ->select('towns.*')
        // ->join('provinces', 'towns.province_id', '=', 'provinces.id')
        // ->where('provinces.country_id', '!=', 1)
        // ->get();

        $data = DB::connection('mysql2')->table('towns')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'poblaciones']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('towns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Town;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->province_id = $r->province_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 7. Importar centros de trabajo.
     */
    public function importWorkplaces()
    {
        $data = DB::connection('mysql2')->table('workplaces')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'centros de trabajo']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('workplaces')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Workplace;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->featured = $r->featured;
                $i->logo = $r->logo;
                $i->address = $r->address;
                $i->cp = $r->cp;
                $i->town_id = $r->town_id;
                $i->nif = $r->nif;
                $i->website = $r->website;
                $i->description = $r->description;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 8. Importar centros de coste.
     */
    public function importCostCenters()
    {
        $data = DB::connection('mysql2')->table('cost_centers')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'centros de coste']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('cost_centers')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new CostCenters;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 9. Importar áreas de negocio.
     */
    public function importBusinessAreas()
    {
        $data = DB::connection('mysql2')->table('business_areas')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'áreas de negocio']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('business_areas')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new BusinessAreas;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 10. Importar Usuarios por empresas.
     */
    public function importUserCompanies()
    {
        $data = DB::connection('mysql2')->table('user_companies')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'usuarios por empresas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_companies')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new UserCompanies;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->company_id = $r->company_id;
                $i->categories = $r->categories;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();

                //Asignamos roles de empresa a user. A las empresas ADN se les genera -de entrada- role de manager. Al resto role básico.
                $adn_companies = [3, 4, 8, 202, 227, 275];

                if (in_array($r->company_id, $adn_companies)) {
                    $company_roles = Role::where('company_id', $r->company_id)->orderBy('id', 'ASC')->first();
                } else {
                    $company_roles = Role::where('company_id', $r->company_id)->orderBy('id', 'DESC')->first();
                }

                $user = User::find($i->user_id);

                if ($user && $company_roles) {

                    if (!$user->hasRole($company_roles->name)) {
                        if (Arr::exists($user->roles, 0)) {
                            $model_id = Arr::first($user->roles, function ($value) {
                                return $value->pivot->model_id;
                            });

                            if ($model_id->pivot->model_id !== $user->id) {
                                $user->assignRole($company_roles->name);
                            }
                        } else {

                            $user->assignRole($company_roles->name);

                        }

                    }
                }


            }
        }
    }

    /**
     * 11. Importar contratos laborales.
     */
    public function importContracts()
    {
        $data = DB::connection('mysql2')->table('contracts')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'contratos laborales']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('contracts')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Contract;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->duration = $r->duration;
                $i->with_holidays = $r->with_holidays;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 12. Tipos de jornada.
     */
    public function importDayTypes()
    {
        $data = DB::connection('mysql2')->table('day_types')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'tipos de jornada']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('day_types')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new DayTypes;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->color = $r->color;
                $i->is_working = $r->is_working;
                $i->working_day = $r->working_day;
                $i->user_enabled = $r->user_enabled;
                $i->cumulative = $r->cumulative;
                $i->rank = $r->rank;
                $i->modifiable = $r->modifiable;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 13. Importar configuraciones tipos de jornada.
     */
    public function importDayTypesSettings()
    {
        $data = DB::connection('mysql2')->table('day_types_configs')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'configuraciones de tipos de jornada']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('day_types_configs')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new DayTypesConfig;
                $i->id = $r->id;
                $i->day_type_id = $r->day_type_id;
                $i->year = $r->year;
                $i->min = $r->min;
                $i->max = $r->max;
                $i->max_per_month = $r->max_per_month;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 14. Importar configuraciones tipos de jornada para usuario.
     */
    public function importDayTypesSettingsUser()
    {
        $data = DB::connection('mysql2')->table('day_types_users_configs')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'configuraciones de tipos de jornada para usuario']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('day_types_users_configs')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new DayTypesUsersConfig;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->day_type_id = $r->day_type_id;
                $i->year = $r->year;
                $i->qty = $r->qty;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     *  15. Importar usuarios por Centros de trabajo.
     */
    public function importUserWorkplaces()
    {
        $data = DB::connection('mysql2')->table('user_workplaces')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'usuarios por Centros de trabajo']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_workplaces')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new UserWorkplaces;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->workplace_id = $r->workplace_id;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 16. Importar usuarios por Centros de coste.
     */
    public function importUserCostCenters()
    {
        $data = DB::connection('mysql2')->table('user_cost_centers')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'usuarios por Centros de coste']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_cost_centers')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new UserCostCenters;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->cost_center_id = $r->cost_center_id;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 17. Importar usuarios por Áreas de negocio.
     */
    public function importUserBusinessAreas()
    {
        $data = DB::connection('mysql2')->table('user_business_areas')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'usuarios por Áreas de negocio']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_business_areas')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new UserBusinessAreas;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->business_area_id = $r->business_area_id;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 18. Importar configuraciones Áreas de negocio.
     */
    public function importBusinessAreasSettings()
    {
        $data = DB::connection('mysql2')->table('business_area_settings')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'configuraciones de Áreas de negocio']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('business_area_settings')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new BusinessAreaSettings;
                $i->id = $r->id;
                $i->ba_id = $r->ba_id;
                $i->provider_id = $r->provider_id;
                $i->custom_invoice = $r->custom_invoice;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 19. Importar empleados.
     *
     * **¡ATENCIÓN! revisar los resultados de la importación con workplace_id = NULL.
     */
    public function importEmployees()
    {
        $data = DB::connection('mysql2')->table('employees')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'empleados']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('employees')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                //Obtenemos worplace_id, campo que no existía en la anterior BD.
                $workplace = UserWorkplaces::select('user_workplaces.workplace_id')
                    ->join('workplaces', 'user_workplaces.workplace_id', '=', 'workplaces.id')
                    ->join('contracts', 'workplaces.company_id', '=', 'contracts.company_id')
                    ->where('user_workplaces.user_id', $r->user_id)
                    ->where('contracts.id', $r->contract_id)
                    ->first();

                if (!$workplace) {
                    $workplace = UserWorkplaces::select('workplace_id')
                        ->where('user_id', $r->user_id)
                        ->orderBy('id', 'DESC')
                        ->first();
                }

                $i = new Employee;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->contract_id = $r->contract_id;
                $i->workplace_id = $workplace ? $workplace->workplace_id : NULL;
                $i->job_title = $r->job_title;
                $i->start_date = $r->start_date;
                $i->end_date = $r->end_date;
                $i->weekly_hours = $r->weekly_hours;
                $i->time_control = $r->time_control;
                $i->year_salary = $r->year_salary;
                $i->status = $r->status;
                $i->observations = $r->observations;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 20. Importar cuentas.
     */
    public function importAccounts()
    {
        $data = DB::connection('mysql2')->table('accounts')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'cuentas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('accounts')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Account;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->description = $r->description;
                $i->rate = $r->rate;
                $i->duration = $r->duration;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 21. Importar cuentas por empresas.
     */
    public function importCompanyAccounts()
    {
        $data = DB::connection('mysql2')->table('company_accounts')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'cuentas por empresas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('company_accounts')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new CompanyAccounts;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->guardian = $r->guardian;
                $i->account_id = $r->account_id;
                $i->start_date = $r->start_date;
                $i->end_date = $r->end_date;
                $i->price = $r->price;
                $i->payment_date = $r->payment_date;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 22. Importar imágenes por usuario.
     */
    public function importUserImages()
    {
        $data = DB::connection('mysql2')->table('user_images')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'imágenes por usuario']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_images')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new UserImage;
                $i->id = $r->id;
                $i->image = $r->image;
                $i->user_id = $r->user_id;
                $i->featured = $r->featured;
                //$i->public = '0';     //Campo nuevo. Toma el valor por defecto.
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 23. Importar teléfonos.
     */
    public function importPhones()
    {
        $data = DB::connection('mysql2')->table('phones')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'teléfonos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('phones')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Phone;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->workplace_id = $r->workplace_id;
                $i->phone_number = $r->phone_number;
                $i->concept = $r->concept;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 24. Importar categorías.
     */
    public function importCategories()
    {
        $data = DB::connection('mysql2')->table('categories')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'categorías']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('categories')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Category;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->module_id = NULL;          //Campo nuevo. Toma el valor por defecto.
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->translations = $r->translations;
                $i->subcategory = $r->subcategory;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 25. Importa módulos por empresa.
     */
    public function importCompanyModules()
    {
        $data = DB::connection('mysql2')->table('company_modules')
            ->select('company_modules.*', 'modules.slug')
            ->join('modules', 'company_modules.module_id', '=', 'modules.id')
            ->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'módulos por empresas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('company_modules')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $module = Module::select('id')
                    ->where('label', $r->slug)
                    ->first();

                $i = new CompanyModules;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->module_id = $module->id;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 26. Importar configuraciones por empresa.
     */
    public function importCompanySettings()
    {
        $data = DB::connection('mysql2')->table('company_settings')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'configuraciones por empresa']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('company_settings')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new CompanySettings;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->currency_id = 2;        //ID del €
                $i->customers_management = $r->customers_management;
                $i->providers_management = $r->providers_management;
                $i->validate_nif = '0';
                $i->primary_color = $r->primary_color;
                $i->secondary_color = $r->secondary_color;
                $i->base_color_budgets = $r->base_color_budgets;
                $i->base_color_orders = $r->base_color_orders;
                $i->base_color_invoices = $r->base_color_invoices;
                $i->iva = $r->iva;
                $i->ip = $r->ip;
                $i->emails = $r->emails;
                //$i->public_info = NULL;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 27. Importar grupos.
     */
    public function importGroups()
    {
        $data = DB::connection('mysql2')->table('groups')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'grupos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('groups')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Group;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->description = $r->description;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 28. Importar miembros de grupos.
     */
    public function importGroupMembers()
    {
        $data = DB::connection('mysql2')->table('group_members')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'miembros de grupos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('group_members')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new GroupMembers;
                $i->id = $r->id;
                $i->group_id = $r->group_id;
                $i->user_id = $r->user_id;
                $i->rol = $r->rol;
                $i->responsible = $r->responsible;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 29. Importar notificaciones.
     */
    public function importNotifications()
    {
        $data = DB::connection('mysql2')->table('notifications')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'notificaciones']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('notifications')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Notification;
                $i->id = $r->id;
                $i->type = $r->type;
                $i->notifiable_type = $r->notifiable_type;
                $i->notifiable_id = $r->notifiable_id;
                $i->data = $r->data;
                $i->read_at = $r->read_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 30. Importar preferencias.
     */
    public function importPreferences()
    {
        $data = DB::connection('mysql2')->table('preferences')
            ->select('preferences.*', 'modules.slug')
            ->leftJoin('modules', 'preferences.module_id', '=', 'modules.id')
            ->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'preferencias']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('preferences')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $module = Module::select('id')
                    ->where('label', $r->slug)
                    ->first();

                $i = new Preference;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->url = $r->url;
                $i->user_id = $r->user_id;
                $i->module_id = $module ? $module->id : NULL;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 31. Importar almacenes.
     */
    public function importStores()
    {
        $data = DB::connection('mysql2')->table('stores')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'almacenes']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('stores')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Store;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->observations = $r->observations;
                $i->type = $r->type;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 32. Importar patrones de producto.
     */
    public function importProductPatterns()
    {
        $data = DB::connection('mysql2')->table('product_patterns')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'patrones de producto']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('product_patterns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ProductPatterns;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->pattern = $r->pattern;
                $i->company_id = $r->company_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 33. Importar items de horario.
     *
     * **¡ATENCIÓN! revisar el campo basic_item pues es nuevo.  Los items de Entrada y Salida básicos deben considerarse como basic_item.
     * **¡ATENCIÓN! revisar el campo own_ouptput pues es nuevo. Revisar los items que puedan tener item propio de salida.
     */
    public function importTimetableItems()
    {
        $data = DB::connection('mysql2')->table('timetable_items')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'items de horario']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('timetable_items')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new TimetableItems;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                //$i->basic_item = '0';         //Revisar tras la importación, pues este campo es nuevo
                $i->type = $r->type;
                //$i->own_output = NULL;        //Revisar tras la importación, pues este campo es nuevo
                $i->fixed = $r->fixed;
                $i->icon = $r->icon;
                $i->min = $r->min;
                $i->max = $r->max;
                $i->qty = $r->qty;
                $i->min_hour = $r->min_hour;
                $i->color = $r->color;
                $i->reviewable = $r->reviewable;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 34. Importar horarios firmados.
     */
    public function importTimetableSigned()
    {
        $data = DB::connection('mysql2')->table('timetable_signed')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'horarios firmados']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('timetable_signed')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new TimetableSigned;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->company_id = $r->company_id;
                $i->month = $r->month;
                $i->year = $r->year;
                $i->signed = $r->signed;
                $i->doc = $r->doc;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 35. Importar horarios.
     */
    public function importTimetables()
    {
        $data = DB::connection('mysql2')->table('timetables')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'horarios']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('timetables')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Timetable;
                $i->id = $r->id;
                $i->date = $r->date;
                $i->hour = $r->hour;
                $i->user_id = $r->user_id;
                $i->item_id = $r->item_id;
                $i->flag = $r->flag;
                $i->revision = $r->revision;
                $i->comments = $r->comments;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 36. Importar horarios de usuario.
     */
    public function importUserTimetables()
    {
        $data = DB::connection('mysql2')->table('user_timetables')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'horarios de usuario']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_timetables')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new UserTimetables;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->employee_id = $r->employee_id;
                $i->from = $r->from;
                $i->to = $r->to;
                $i->daily_hours = $r->daily_hours;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 37. Importar calendarios laborales.
     */
    public function importWorkCalendars()
    {
        $data = DB::connection('mysql2')->table('work_calendars')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'calendarios laborales']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('work_calendars')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new WorkCalendar;
                $i->id = $r->id;
                $i->workplace_id = $r->workplace_id;
                $i->day_type_id = $r->day_type_id;
                $i->date = $r->date;
                $i->hours = $r->hours;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 38. Importar calendarios laborales de usuario.
     */
    public function importWorkUserCalendars()
    {
        $data = DB::connection('mysql2')->table('work_user_calendars')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'calendarios laborales de usuario']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('work_user_calendars')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new WorkUserCalendar;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->day_type_id = $r->day_type_id;
                $i->date = $r->date;
                $i->self_hours = $r->self_hours;
                $i->notified = $r->notified;
                $i->doc = $r->doc;
                $i->status = $r->status;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 39. Importar fechas acumuladas del calendario laboral.
     */
    public function importResiduaryDays()
    {
        $data = DB::connection('mysql2')->table('residuary_days')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'fechas acumuladas del calendario laboral']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('residuary_days')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ResiduaryDays;
                $i->id = $r->id;
                $i->user_id = $r->user_id;
                $i->day_type_id = $r->day_type_id;
                $i->year = $r->year;
                $i->days = $r->days;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 40. Importar formularios de presupuesto.
     */
    public function importBudgetForms()
    {
        $data = DB::connection('mysql2')->table('budget_forms')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'formularios para presupuestos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('budget_forms')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new BudgetForms;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->conditions = $r->conditions;
                $i->labor_journeys = $r->labor_journeys;
                $i->status = 1;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 41. Importar productos.
     */
    public function importProducts()
    {
        $data = DB::connection('mysql2')->table('products')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'productos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('products')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Product;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->ref = $r->ref;
                $i->product_pattern_id = $r->product_pattern_id;
                $i->manual_ref = $r->manual_ref;
                $i->translations = $r->translations;
                $i->tags = $r->tags;
                $i->description = $r->description;
                $i->long_description = $r->long_description;
                $i->company_id = $r->company_id;
                $i->user_id = $r->user_id;
                $i->type = $r->type;
                $i->price = $r->price;
                //$i->calculated_by_percent = NULL;  //Campo nuevo. Obtener valores de los items de formulario cuyo precio se obtenía por porcentaje.
                $i->iva = $r->iva;
                $i->batch = $r->batch;
                $i->stock_management = $r->stock_management;
                $i->on_sale = $r->on_sale;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 42. Importar patrones de presupuestos.
     */
    public function importBudgetPatterns()
    {
        $data = DB::connection('mysql2')->table('budget_patterns')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'patrones de presupuestos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('budget_patterns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new BudgetPatterns;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->pattern = $r->pattern;
                $i->yearly_reset = $r->yearly_reset;
                $i->company_id = $r->company_id;
                //$i->status = $r->status;      //Campo nuevo. Toma valor por defecto.
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 43. Importar configuraciones de presupuestos.
     */
    public function importBudgetSettings()
    {
        $data = DB::connection('mysql2')->table('budget_settings')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'configuraciones de presupuestos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('budget_settings')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new BudgetSettings;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->conditions = $r->conditions;
                $i->payment_method = $r->payment_method;
                $i->observations = $r->observations;
                $i->footer_legend = $r->footer_legend;
                $i->featured = $r->featured;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 44. Importar patrones de pedidos.
     */
    public function importOrderPatterns()
    {
        $data = DB::connection('mysql2')->table('order_patterns')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'patrones de pedidos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('order_patterns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new OrderPatterns;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->pattern = $r->pattern;
                $i->yearly_reset = $r->yearly_reset;
                $i->company_id = $r->company_id;
                //$i->status = $r->status;      //Campo nuevo. Toma valor por defecto.
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 45. Importar configuraciones de pedidos.
     */
    public function importOrderSettings()
    {
        $data = DB::connection('mysql2')->table('order_settings')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'configuraciones de pedidos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('order_settings')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new OrderSettings;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->conditions = $r->conditions;
                $i->payment_method = $r->payment_method;
                $i->observations = $r->observations;
                $i->footer_legend = $r->footer_legend;
                $i->featured = $r->featured;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 46. Importar patrones de compras.
     */
    public function importPurchasePatterns()
    {
        $data = DB::connection('mysql2')->table('purchase_patterns')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'patrones de compras']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('purchase_patterns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new PurchasePatterns;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->pattern = $r->pattern;
                $i->yearly_reset = $r->yearly_reset;
                $i->company_id = $r->company_id;
                //$i->status = $r->status;      //Campo nuevo. Toma valor por defecto.
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 47. Importar pedidos.
     *
     * Tener en cuenta que en la nueva versión, presupuestos y pedidos se agrupan bajo una misma tabla -orders- y que comparten id.
     *
     * ** ¡ATENCIÓN! la importación da error por exceder la memoria: Allowed memory size of 536870912 bytes exhausted (tried to allocate 33558528 bytes)
     */
    public function importOrders()
    {
        //Inicialmente cargamos los registros de la tabla orders.
        $data = DB::connection('mysql2')->table('orders')->get();
        //$data = false;

        //Array para guardar los id de presupuestos con pedido:
        $budgetsWithOrder = [];

        if ($data && $data->count()) {
            Importaciones::create(['texto' => 'pedidos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('orders')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                //Datos del presupuesto:
                $budget = false;
                if ($r->budget_id) {
                    $budget = DB::connection('mysql2')->table('budgets')
                        ->where('id', $r->budget_id)
                        ->first();

                    array_push($budgetsWithOrder, $r->budget_id);
                }

                $i = new Order;
                $i->id = $r->id;
                $i->is_budget = $r->budget_id ? 1 : '0';
                $i->is_order = 1;   //Inicialmente se obtienen de la BD antigua los pedidos.
                $i->name = $r->name ? $r->name : ($budget ? $budget->name : NULL);
                $i->slug = $r->name ? Str::slug($r->name) : ($budget ? $budget->slug : NULL);
                $i->recid = $budget ? $budget->recid : NULL;
                $i->budget_num = $budget ? $budget->bkey : NULL;
                $i->budget_num_cardinal = $budget ? $budget->bkey_num : NULL;
                $i->budget_pattern_id = $budget ? $budget->budget_pattern_id : NULL;
                $i->budget_old_num = $budget ? $budget->old_budget : NULL;
                $i->modality = $budget ? $budget->modality : NULL;
                $i->form_id = $budget ? $budget->form_id : NULL;
                $i->accepted_date = $budget ? $budget->accepted_date : NULL;
                $i->accepted_by = $budget ? $budget->accepted_by : NULL;
                $i->budget_status = $budget ? $budget->status : '0';
                $i->description = $budget ? $budget->description : NULL;
                $i->comments = $budget ? $budget->comments : NULL;
                $i->budget_file_name = $budget ? $budget->file_name : NULL;
                $i->budget_template_id = $budget ? $budget->template_id : NULL;
                $i->budget_template = $budget ? $budget->template : NULL;
                $i->order_parent = NULL;        //Para las agrupaciones
                $i->is_group = '0';
                $i->order_num = $r->order_num;
                $i->order_num_cardinal = $r->order_serial;
                $i->order_pattern_id = $r->order_pattern_id;
                $i->order_old_num = $r->old_order;
                $i->estimated_delivery_date = $r->estimated_delivery_date;
                $i->delivery_date = $r->delivery_date;
                $i->briefing = $r->briefing;
                $i->observations = $r->observations;
                //$i->observations_customer = NULL;     //Campo nuevo. Toma el valor por defecto.
                $i->order_template_id = $r->template_id;
                $i->order_template = $r->template;
                $i->order_status = $r->status;
                $i->purchase_num = $r->purchase_num;
                $i->purchase_num_cardinal = $r->purchase_serial;
                $i->purchase_pattern_id = $r->purchase_pattern_id;
                $i->purchase_old_num = $r->old_purchase;
                $i->purchase_doc = NULL;
                $i->pay_order = NULL;                   //Campo nuevo. Toma el valor por defecto.
                $i->pay_order_received = NULL;          //Campo nuevo. Toma el valor por defecto.
                $i->customer_id = $r->customer_id;
                $i->customer_manager_id = $r->buyer;
                $i->customer_workplace_id = $r->workplace_id;
                $i->customer_cost_center_id = $r->cost_center_id;
                $i->customer_business_area_id = $r->business_area_id;
                $i->customer_order = NULL;
                $i->customer_order_doc = NULL;
                $i->customer_team = $r->customer_team;
                $i->customer_products = $r->customer_product;
                $i->confirmed_by = $r->buyer;
                $i->confirm_code = $r->confirm_code;
                $i->confirm_code_date = $r->confirm_code_date;
                $i->provider_id = $r->provider_id;
                $i->provider_manager_id = $r->owner;
                $i->provider_workplace_id = $r->provider_workplace;
                $i->provider_cost_center_id = NULL;
                $i->provider_business_area_id = NULL;
                $i->provider_team = $r->provider_team;
                $i->final_customer_id = $r->final_customer_id;
                $i->final_customer_manager_id = NULL;
                $i->created_by = $r->user_id;
                $i->discount = $r->discount;
                $i->discount_amount = $r->discount_amount;
                $i->bag_amount = '0';
                $i->invoiced = $r->invoiced;
                //$i->prepare_delivery = '0';       //Campo nuevo. Toma el valor por defecto.
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;

                //Campos temporales.  A eliminar una vez concluida y validada la migración.
                $i->old_budget_id = $budget ? $budget->id : NULL;
                $i->old_order_id = $r->id;

                $i->save();
            }

            //Obtenemos los presupuestos sin pedido. Generan id nuevo.
            $budgets = DB::connection('mysql2')->table('budgets')
                ->whereNotIn('id', $budgetsWithOrder)
                ->get();

            foreach ($budgets as $b) {
                $i = new Order;
                $i->is_budget = 1;
                $i->is_order = '0';
                $i->name = $b->name;
                $i->slug = $b->slug;
                $i->recid = $b->recid;
                $i->budget_num = $b->bkey;
                $i->budget_num_cardinal = $b->bkey_num;
                $i->budget_pattern_id = $b->budget_pattern_id;
                $i->budget_old_num = $b->old_budget;
                $i->modality = $b->modality;
                $i->form_id = $b->form_id;
                $i->accepted_date = $b->accepted_date;
                $i->accepted_by = $b->accepted_by;
                $i->budget_status = $b->status;
                $i->description = $b->description;
                $i->comments = $b->comments;
                $i->budget_file_name = $b->file_name;
                $i->budget_template_id = $b->template_id;
                $i->budget_template = $b->template;
                // $i->order_parent = NULL;        //Para las agrupaciones
                // $i->is_group = '0';
                // $i->order_num = NULL;
                // $i->order_num_cardinal = NULL;
                // $i->order_pattern_id = NULL;
                // $i->order_old_num = NULL;
                $i->estimated_delivery_date = NULL;
                $i->delivery_date = $b->delivery_date;
                // $i->briefing = NULL;
                // $i->observations = NULL;
                // $i->observations_customer = NULL;     //Campo nuevo. Toma el valor por defecto.
                // $i->order_template_id = NULL;
                // $i->order_template = NULL;
                // $i->order_status = '0';
                // $i->purchase_num = $b->purchase_num;
                // $i->purchase_num_cardinal = $b->purchase_serial;
                // $i->purchase_pattern_id = $b->purchase_pattern_id;
                // $i->purchase_old_num = $b->oc;
                // $i->purchase_doc = $b->oc_doc;
                // $i->pay_order = NULL;                   //Campo nuevo. Toma el valor por defecto.
                // $i->pay_order_received = NULL;          //Campo nuevo. Toma el valor por defecto.
                $i->customer_id = $b->customer_id;
                $i->customer_manager_id = $b->manager_customer_id;
                $i->customer_workplace_id = $b->customer_workplace;
                // $i->customer_cost_center_id = $b->cost_center_id;
                // $i->customer_business_area_id = $b->business_area_id;
                $i->customer_order = $b->oc;
                $i->customer_order_doc = $b->oc_doc;
                // $i->customer_team = $b->customer_team;
                // $i->customer_products = $b->customer_product;
                // $i->confirmed_by = $b->buyer;
                // $i->confirm_code = $b->confirm_code;
                // $i->confirm_code_date = $b->confirm_code_date;
                $i->provider_id = $b->provider_id;
                $i->provider_manager_id = $b->manager_provider_id;
                $i->provider_workplace_id = $b->provider_workplace;
                // $i->provider_cost_center_id = NULL;
                // $i->provider_business_area_id = NULL;
                // $i->provider_team = $b->provider_team;
                $i->final_customer_id = $b->final_customer_id;
                $i->final_customer_manager_id = $b->manager_final_customer_id;
                // $i->created_by = $b->user_id;
                $i->discount = $b->discount;
                // $i->discount_amount = $b->discount_amount;
                // $i->bag_amount = '0';
                // $i->invoiced = $b->invoiced;
                $i->deleted_at = $b->deleted_at;
                $i->created_at = $b->created_at;
                $i->updated_at = $b->updated_at;

                //Campos temporales.  A eliminar una vez concluida y validada la migración.
                $i->old_budget_id = $b->id;
                $i->old_order_id = NULL;

                $i->save();
            }
        }

        //Incluimos las agrupaciones de pedidos. También generan id nuevo.
        $order_groups = DB::connection('mysql2')->table('order_groups')->get();

        foreach ($order_groups as $og) {
            //Datos del presupuesto:
            $budget = false;
            if ($og->budget_id) {
                $budget = DB::connection('mysql2')->table('budgets')
                    ->where('id', $og->budget_id)
                    ->first();
            }

            $i = new Order;
            $i->is_budget = '0';
            $i->is_order = 1;
            $i->name = $og->name;
            $i->slug = $og->name ? Str::slug($og->name) : NULL;
            $i->recid = $budget ? $budget->recid : NULL;
            $i->budget_num = $budget ? $budget->bkey : NULL;
            $i->budget_num_cardinal = $budget ? $budget->bkey_num : NULL;
            $i->budget_pattern_id = $budget ? $budget->budget_pattern_id : NULL;
            $i->budget_old_num = $budget ? $budget->old_budget : NULL;
            $i->modality = $budget ? $budget->modality : NULL;
            $i->form_id = $budget ? $budget->form_id : NULL;
            $i->accepted_date = $budget ? $budget->accepted_date : NULL;
            $i->accepted_by = $budget ? $budget->accepted_by : NULL;
            $i->budget_status = $budget ? $budget->status : '0';
            $i->description = $budget ? $budget->description : NULL;
            $i->comments = $budget ? $budget->comments : NULL;
            $i->budget_file_name = $budget ? $budget->file_name : NULL;
            $i->budget_template_id = $budget ? $budget->template_id : NULL;
            $i->budget_template = $budget ? $budget->template : NULL;
            $i->order_old_num = $og->old_order_group;
            $i->order_status = $og->status;
            $i->pay_order = $og->pay_order;
            $i->pay_order_received = $og->pay_order_received;
            $i->customer_id = $og->customer_id;
            $i->provider_id = $og->provider_id;
            $i->is_group = 1;
            $i->created_by = $og->user_id;
            $i->deleted_at = $og->deleted_at;
            $i->created_at = $og->created_at;
            $i->updated_at = $og->updated_at;
            $i->save();

            //Pedidos del grupo:
            $pedidos_grupo = DB::connection('mysql2')->table('order_groups_orders')
                ->select('order_id')
                ->where('og_id', $og->id)
                ->get();

            foreach ($pedidos_grupo as $pg) {
                Order::where('id', $pg->order_id)
                    ->update(['order_parent' => $i->id]);
            }
        }
    }

    /**
     * 48. Importar relación clientes - proveedores.
     */
    public function importCustomerProviders()
    {
        $data = DB::connection('mysql2')->table('customer_providers')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'clientes - proveedores']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('customer_providers')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new CustomerProviders;
                $i->id = $r->id;
                $i->customer_id = $r->customer_id;
                $i->provider_id = $r->provider_id;
                $i->acronym = $r->acronym;
                $i->pay_after_days = $r->pay_after_days;
                $i->payday = $r->payday;
                $i->ba_id = $r->ba_id;
                $i->workplace_id = $r->workplace_id;
                $i->cc_id = $r->cc_id;
                $i->discount = $r->discount;
                //$i->credit_risk = $r->credit_risk;    //Campo nuevo. Toma valor por defecto.
                $i->billing_comunication = $r->billing_comunication;
                $i->budget_form_id = $r->budget_form_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 49. Importar documentos de pedidos.
     */
    public function importOrderDocs()
    {
        $data = DB::connection('mysql2')->table('order_docs')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'documentos de pedidos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('order_docs')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                //Obtenemos el order_id actualizado:
                $order = Order::select('id')
                    ->where('old_order_id', $r->order_id)
                    ->first();

                $i = new OrderDocs;
                $i->id = $r->id;
                $i->doc = $r->doc;
                $i->order_id = $order ? $order->id : $r->order_id;
                //$i->type = $r->type;      //Campo nuevo. Toma valor por defecto.
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 50. Importar productos por cliente.
     */
    public function importCustomerProducts()
    {
        $data = DB::connection('mysql2')->table('customer_products')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'productos por cliente']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('customer_products')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new CustomerProducts;
                $i->id = $r->id;
                $i->product_id = $r->product_id;
                $i->provider_id = $r->provider_id;
                $i->customer_id = $r->customer_id;
                $i->ba_id = $r->ba_id;
                $i->cc_id = $r->cc_id;
                $i->price = $r->price;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 51. Importar patrones de incidencias.
     */
    public function importIncidencePatterns()
    {
        $data = DB::connection('mysql2')->table('incidence_patterns')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'patrones de incidencias']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('incidence_patterns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new IncidencePatterns;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->pattern = $r->pattern;
                $i->yearly_reset = $r->yearly_reset;
                $i->company_id = $r->company_id;
                //$i->status = $r->status;      //Campo nuevo. Toma valor por defecto.
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 52. Importar incidencias.
     *
     * ** ¡ATENCIÓN! Completar el campo incidence_num_cardinal pues de la tabla antigua no proviene la numeración.
     */
    public function importIncidences()
    {
        $data = DB::connection('mysql2')->table('incidences')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'incidencias']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('incidences')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Incidence;
                $i->id = $r->id;
                $i->incidence_num = $r->incidence_num;
                //$i->incidence_num_cardinal = $r->incidence_num_cardinal;      //  ** ¡ATENCIÓN>
                $i->origin = $r->origin;
                $i->user_id = $r->user_id;
                $i->provider_id = $r->provider_id;
                $i->provider_manager_id = $r->provider_manager;
                $i->customer_id = $r->customer_id;
                $i->customer_manager_id = $r->customer_manager;
                $i->observations = $r->observations;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 53. Importar atributos.
     *
     * Antigua tabla parameters.
     */
    public function importAttributes()
    {
        $data = DB::connection('mysql2')->table('parameters')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'atributos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('attributes')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Attribute;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->description = $r->description;
                $i->datatype = $r->datatype;
                $i->values = $r->values;
                $i->company_id = $r->company_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 54. Importar atributos de producto.
     */
    public function importProductAttributes()
    {
        $data = DB::connection('mysql2')->table('product_parameters')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'atributos de productos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('product_attributes')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ProductAttributes;
                $i->id = $r->id;
                $i->product_id = $r->product_id;
                $i->attribute_id = $r->parameter_id;
                $i->value = $r->value;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 55. Importar números de serie de productos.
     */
    public function importProductSerials()
    {
        $data = DB::connection('mysql2')->table('product_serials')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'números de serie de productos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('product_serials')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ProductSerials;
                $i->id = $r->id;
                $i->product_id = $r->product_id;
                $i->serial = $r->serial;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 56. Importar attributos por número de serie.
     */
    public function importProductSerialsCards()
    {
        $data = DB::connection('mysql2')->table('product_serials_cards')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'atributos de números de serie']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('product_serials_cards')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ProductSerialsCards;
                $i->id = $r->id;
                $i->product_serial_id = $r->product_serial_id;
                $i->attribute_id = $r->parameter_id;
                $i->value = $r->value;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 57. Importar líneas de incidencia.
     */
    public function importIncidenceLines()
    {
        $data = DB::connection('mysql2')->table('incidence_lines')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'líneas de incidencias']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('incidence_lines')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new IncidenceLines;
                $i->id = $r->id;
                $i->incidence_id = $r->incidence_id;
                $i->product_id = $r->product_id;
                $i->qty = $r->qty;
                $i->batch_id = $r->batch_id;
                $i->product_serial_id = $r->product_serial_id;
                $i->observations = $r->observations;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 58. Importar documentos de productos.
     */
    public function importProductDocs()
    {
        $data = DB::connection('mysql2')->table('product_docs')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'documentos de productos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('product_docs')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ProductDocs;
                $i->id = $r->id;
                $i->doc = $r->doc;
                $i->product_id = $r->product_id;
                //$i->type = $r->type;      //Nuevo campo.
                $i->featured = $r->featured;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 59. Importar precios de productos.
     */
    public function importProductPrices()
    {
        $data = DB::connection('mysql2')->table('product_prices')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'precios de productos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('product_prices')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ProductPrices;
                $i->id = $r->id;
                $i->product_id = $r->product_id;
                $i->price = $r->price;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 60. Importar trackings de pedidos.
     */
    public function importOrderTrackings()
    {
        $data = DB::connection('mysql2')->table('order_trackings')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'tracking de pedidos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('order_trackings')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new OrderTracking;
                $i->id = $r->id;
                $i->order_id = $r->order_id;
                $i->user_id = $r->user_id;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 61. Importar items de formulario de presupuestos.
     */
    public function importBudgetFormItems()
    {
        $data = DB::connection('mysql2')->table('budget_form_items')
            ->select('budget_form_items.*', 'budget_forms.company_id')
            ->leftJoin('budget_forms', 'budget_form_items.form_id', '=', 'budget_forms.id')
            ->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'items de formulario de presupuestos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('budget_form_items')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                //Sincronizamos el budget_id de la BD antigua con el order_id actual.
                $order_id = NULL;
                if ($r->budget_id) {
                    $order = Order::select('id')
                        ->where('old_budget_id', $r->budget_id)
                        ->first();

                    $order_id = $order ? $order->id : NULL;
                }

                //Cada item perteneciente a un bloque debe crearse como producto.
                $product_id = NULL;
                if ($r->recid > 0) {
                    //Comprobamos por company_id y el campo 'name' que no exista previamente en la tabla products.
                    $product = Product::select('id', 'price', 'calculated_by_percent')
                        ->where('name', $r->name)
                        ->where('company_id', $r->company_id)
                        ->first();

                    if ($product) {
                        $product->price = $r->price;
                        $product->calculated_by_percent = $r->calculate_by_percent;
                        $product->save();

                        $product_id = $product->id;

                    } else {
                        $p = new Product;
                        $p->name = $r->name;
                        $p->slug = $r->name ? Str::slug($r->name) : 'producto-sin-nombre';
                        $p->company_id = $r->company_id;
                        $p->type = 'p';
                        $p->price = $r->price;
                        $p->calculated_by_percent = $r->calculate_by_percent;
                        $p->iva = 21;
                        $p->on_sale = 1;
                        $p->status = 1;
                        $p->save();

                        $product_id = $p->id;
                    }
                }

                $i = new BudgetFormItems;
                $i->id = $r->id;
                $i->recid = $r->recid;
                $i->form_id = $r->form_id;
                $i->order_id = $order_id;
                $i->product_id = $product_id;    //Campo nuevo.
                $i->name = $r->name;
                $i->translations = $r->translations;
                $i->observations = $r->observations;
                $i->discount = $r->discount;
                $i->qty_min = $r->qty_min;
                $i->calculated_by_percent = $r->calculate_by_percent;
                $i->fixed_in_budget = $r->fixed_in_budget;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 62. Importar agendas.
     */
    public function importSchedules()
    {
        $data = DB::connection('mysql2')->table('schedules')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'agendas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('schedules')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Schedule;
                $i->id = $r->id;
                $i->company_id = $r->company_id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->description = $r->description;
                $i->by_default = $r->by_default;
                $i->user_id = $r->user_id;
                $i->authorized_users = $r->authorized_users;
                $i->day_type_id = $r->day_type_id;
                $i->color = $r->color;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 63. Importar tipos de evento.
     */
    public function importScheduleEventTypes()
    {
        $data = DB::connection('mysql2')->table('schedule_event_types')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'tipos de evento']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('schedule_event_types')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ScheduleEventTypes;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->user_id = $r->user_id;
                $i->translations = $r->translations;
                $i->description = $r->description;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 64. Importar eventos de agenda.
     */
    public function importScheduleEvents()
    {
        $data = DB::connection('mysql2')->table('schedule_events')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'eventos de agenda']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('schedule_events')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ScheduleEvents;
                $i->id = $r->id;
                $i->title = $r->title;
                $i->slug = $r->slug;
                $i->description = $r->description;
                $i->schedule_id = $r->schedule_id;
                $i->author = $r->author;
                $i->start = $r->start;
                $i->end = $r->end;
                $i->allDay = $r->allDay;
                $i->responsible = $r->responsible;
                $i->guests = $r->guests;
                $i->products = $r->products;
                $i->place = $r->place;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 65. Importar lotes.
     */
    public function importBatches()
    {
        $data = DB::connection('mysql2')->table('batches')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'lotes']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('batches')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Batch;
                $i->id = $r->id;
                $i->product_id = $r->product_id;
                //$i->work_order_id = NULL;     //Campo nuevo
                $i->name = $r->name;
                $i->deadline = $r->deadline;
                $i->expiry_date = $r->expiry_date;
                $i->initial_qty = $r->initial_qty;
                $i->doc_num = $r->doc_num;
                $i->doc = $r->doc;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 66. Importar patrones de factura.
     */
    public function importInvoicePatterns()
    {
        $data = DB::connection('mysql2')->table('invoice_patterns')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'patrones de facturas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('invoice_patterns')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new InvoicePatterns;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->pattern = $r->pattern;
                $i->yearly_reset = $r->yearly_reset;
                $i->company_id = $r->company_id;
                //$i->status = $r->status;      //Campo nuevo. Toma valor por defecto.
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 67. Importar configuraciones de facturas.
     */
    public function importInvoiceSettings()
    {
        $data = DB::connection('mysql2')->table('invoice_settings')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'configuraciones de facturas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('invoice_settings')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new InvoiceSettings;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->conditions = $r->conditions;
                $i->payment_method = $r->payment_method;
                $i->observations = $r->observations;
                $i->featured = $r->featured;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 68. Importar facturas.
     */
    public function importInvoices()
    {
        $data = DB::connection('mysql2')->table('invoices')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'facturas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('invoices')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                //El num_cardinal debe obtenerse a través del patrón de factura utilizado:
                $ip = InvoicePatterns::select('pattern')->find($r->invoice_pattern_id);
                $pattern = $ip && $ip->pattern ? unserialize($ip->pattern) : false;

                if ($pattern) {
                    $digits = end($pattern);
                    $digits_ = $digits ? trim($digits, '|') : false;
                    $n = $digits_ ? strlen($digits_) : false;

                    //Del valor de invoice_num extraemos por la derecha, la cantidad de carácteres que indica su patrón, y convertimos el string a integer:
                    $nums = $r->invoice_num ? substr($r->invoice_num, -$n) : false;
                    $num_cardinal = $nums ? (int)$nums : NULL;
                } else {
                    $num_cardinal = NULL;
                }

                $i = new Invoice;
                $i->id = $r->id;
                $i->refund = $r->refund;
                $i->invoice_num = $r->invoice_num;
                $i->num_cardinal = $num_cardinal;  //Campo nuevo. Calcular su valor
                $i->pattern_id = $r->invoice_pattern_id;
                $i->invoice_old_num = $r->old_invoice;
                $i->order_id = $r->order_id;
                $i->amount = $r->total_amount;
                $i->with_iva = $r->with_iva;
                $i->amount_with_iva = $r->amount_with_iva ? $r->amount_with_iva : '0';
                $i->fiscal_date = $r->fiscal_date;
                $i->expiration_date = $r->expiration_date;
                $i->user_id = $r->user_id;
                $i->observations = $r->comments;
                $i->template_id = $r->template_id;
                $i->template = $r->template;
                $i->printed = $r->printed;
                $i->status = $r->status;
                $i->blocked = $r->blocked;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();

                //Vincular a pedido:
                // $io = new InvoiceOrders;
                // $io->invoice_id = $r->id;
                // $io->order_id = $r->order_id
            }
        }
    }

    /**
     * 69. Importar previsiones de facturación.
     */
    public function importOrderPrevisions()
    {
        $data = DB::connection('mysql2')->table('order_previsions')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'previsiones de facturación']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('order_previsions')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new OrderPrevisions;
                $i->id = $r->id;
                $i->order_id = $r->order_id;
                $i->year = $r->year;
                $i->month = $r->month;
                $i->percent = $r->percent;
                $i->invoice_id = $r->invoice_id;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 70. Importar items de pedidos.
     */
    public function importItems()
    {
        $data = DB::connection('mysql2')->table('items')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'items de pedidos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('items')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Item;
                $i->id = $r->id;
                $i->product_id = $r->product_id;
                $i->order_id = $r->order_id;
                $i->budget_form_block_id = $r->block_id;
                $i->budget_form_item_id = $r->item_id;
                //$i->budget_order = NULL;      //Campo nuevo
                $i->concept = $r->concept;
                $i->observations = $r->observations;
                $i->qty = $r->qty;
                $i->price = $r->price;
                $i->item_no_price = $r->line_no_price;
                $i->iva = $r->iva;
                $i->discount = $r->discount;
                $i->calculated_by_percent = $r->calculated_by_percent;
                $i->invoiced = $r->invoiced;
                $i->deposit_used = $r->deposit_used;
                $i->days = $r->days;
                $i->days_continue = $r->days_continue;
                $i->days_continue_discount = $r->days_continue_discount;
                $i->active = $r->active;
                $i->status = $r->status;
                $i->print = $r->print;
                $i->user_id = $r->user_id;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 71. Importar status de items.
     */
    public function importItemsStatus()
    {
        $data = DB::connection('mysql2')->table('item_status')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'status de items de pedidos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('item_status')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new ItemStatus;
                $i->id = $r->id;
                $i->item_id = $r->item_id;
                $i->provider_observations = $r->provider_observations;
                $i->customer_observations = $r->customer_observations;
                $i->status = $r->status;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 72. Importar proyectos.
     */
    public function importProjects()
    {
        $data = DB::connection('mysql2')->table('projects')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'proyectos']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('projects')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Project;
                $i->id = $r->id;
                // $i->project_num = $r->project_num;               //Campos nuevos
                // $i->num_cardinal = $r->num_cardinal;
                // $i->pattern_id = $r->pattern_id;
                // $i->project_old_num = $r->project_old_num;
                $i->code = $r->code;
                $i->contest_code = $r->contest_code;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->description = $r->description;
                $i->briefing = $r->briefing;
                $i->company_id = $r->company_id;
                $i->user_id = $r->user_id;
                $i->owner = $r->owner;
                $i->team = $r->team;
                // $i->restricted_access = $r->restricted_access;           //Campo nuevo
                // $i->customer_id = $r->customer_id;                       //Campo nuevo
                $i->estimated_delivery_date = $r->estimated_delivery_date;
                $i->delivery_date = $r->delivery_date;
                $i->hours_forecast = $r->hours_forecast;
                // $i->hours_worked = $r->hours_worked;                     //Campo nuevo
                // $i->task_hours = $r->task_hours;                         //Campo nuevo
                $i->completed = $r->completed;
                $i->groups = $r->groups;
                $i->status = $r->status;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 73. Importar estados de tareas de proyecto.
     */
    public function importTaskStatus()
    {
        $data = DB::connection('mysql2')->table('project_status')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'estados de tareas de proyecto']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('task_status')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new TaskStatus;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->company_id = $r->company_id;
                $i->order = $r->order;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 74. Importar tareas.
     */
    public function importTasks()
    {
        $data = DB::connection('mysql2')->table('tasks')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'tareas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('tasks')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new Task;
                $i->id = $r->id;
                $i->name = $r->name;
                $i->slug = $r->slug;
                $i->description = $r->description;
                $i->project_id = $r->project_id;
                $i->user_id = $r->user_id;
                $i->assigned_to = $r->assigned_to;
                $i->delivery_date = $r->delivery_date;
                // $i->estimated_hours = $r->estimated_hours;       //Campo nuevo
                // $i->partial_hours = $r->partial_hours;           //Campo nuevo
                $i->hours = $r->hours;
                //$i->status = NULL;                                //Campo nuevo
                $i->column_order = $r->column_order;
                $i->deleted_at = $r->deleted_at;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 75. Importar documentos de tareas.
     */
    public function importTaskDocs()
    {
        $data = DB::connection('mysql2')->table('task_docs')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'documentos de tareas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('task_docs')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new TaskDocs;
                $i->id = $r->id;
                $i->doc = $r->doc;
                $i->task_id = $r->task_id;
                //$i->featured = $r->featured;                      //Campo nuevo
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 76. Importar mensajes de tareas.
     */
    public function importTaskMessages()
    {
        $data = DB::connection('mysql2')->table('task_messages')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'mensajes de tareas']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('task_messages')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new TaskMessages;
                $i->id = $r->id;
                $i->message = $r->message;
                $i->task_id = $r->task_id;
                $i->from = $r->from;
                $i->to = $r->to;
                $i->taken = $r->taken;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }

    /**
     * 77. Importar líneas de facturas.
     */
    public function importInvoiceItems()
    {
        $data = DB::connection('mysql2')->table('invoice_lines')->get();

        if ($data->count()) {
            Importaciones::create(['texto' => 'líneas de factura']);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('invoice_items')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach ($data as $r) {
                $i = new InvoiceItems;
                $i->id = $r->id;
                $i->invoice_id = $r->invoice_id;
                $i->item_id = $r->item_id;
                $i->qty = $r->qty;
                $i->price = $r->price;
                $i->discount = $r->discount ? $r->discount : '0';
                $i->days = $r->days;
                $i->days_continue = $r->days_continue;
                $i->days_continue_discount = $r->days_continue_discount;
                $i->amount = $r->amount;
                $i->iva = $r->iva;
                $i->created_at = $r->created_at;
                $i->updated_at = $r->updated_at;
                $i->save();
            }
        }
    }


    /**
     * Fin importación.
     */
    public function endImportation()
    {
        Importaciones::create(['texto' => 'Fin de la importación']);
    }
}
