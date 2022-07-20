<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

//Models:
use App\Models\Functionality;
use App\Models\Module;

class FunctionalitiesTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $data = [
            ['name' => 'dashboard',
            'slug' => 'dashboard',
            'label' => 'dashboard',
            'module' => 'dashboard'
            ],
            ['name' => 'chat',
            'slug' => 'chat',
            'label' => 'chat',
            'module' => 'dashboard'
            ],

            ['name' => 'accounts',
            'slug' => 'accounts',
            'label' => 'cuentas',
            'module' => 'settings'
            ],
            ['name' => 'modules',
            'slug' => 'modules',
            'label' => 'modulos',
            'module' => 'settings'
            ],
            ['name' => 'roles',
            'slug' => 'roles',
            'label' => 'roles',
            'module' => 'settings'
            ],
            ['name' => 'permissions',
            'slug' => 'permissions',
            'label' => 'permisos',
            'module' => 'settings'
            ],
            ['name' => 'currencies',
            'slug' => 'currencies',
            'label' => 'monedas',
            'module' => 'settings'
            ],
            ['name' => 'banks',
            'slug' => 'banks',
            'label' => 'bancos',
            'module' => 'settings'
            ],
            ['name' => 'countries',
            'slug' => 'countries',
            'label' => 'paises',
            'module' => 'settings'
            ],
            ['name' => 'contents',
            'slug' => 'contents',
            'label' => 'contenidos',
            'module' => 'settings'
            ],
            ['name' => 'units',
            'slug' => 'units',
            'label' => 'unidades',
            'module' => 'settings'
            ],
            
            ['name' => 'my account',
            'slug' => 'my-account',
            'label' => 'mi_cuenta',
            'module' => 'account'
            ],
            ['name' => 'my modules',
            'slug' => 'my-modules',
            'label' => 'mis_modulos',
            'module' => 'account'
            ],

            ['name' => 'users',
            'slug' => 'users',
            'label' => 'usuarios',
            'module' => 'users'
            ],
            ['name' => 'users all',
            'slug' => 'users-all',
            'label' => 'usuarios_todos',
            'module' => 'users-all'
            ],
            ['name' => 'user categories',
            'slug' => 'user-categories',
            'label' => 'usuarios_categorias',
            'module' => 'users'
            ],
            ['name' => 'ip access',
            'slug' => 'ip-access',
            'label' => 'acceso_ip',
            'module' => 'users'
            ],

            ['name' => 'companies',
            'slug' => 'companies',
            'label' => 'empresas',
            'module' => 'companies'
            ],
            ['name' => 'company settings',
            'slug' => 'company-settings',
            'label' => 'empresas_configuracion_',
            'module' => 'companies'
            ],
            ['name' => 'business areas',
            'slug' => 'business-areas',
            'label' => 'areas_negocio',
            'module' => 'companies'
            ],
            ['name' => 'cost centers',
            'slug' => 'cost-centers',
            'label' => 'centros_coste',
            'module' => 'companies'
            ],
            ['name' => 'workplaces',
            'slug' => 'workplaces',
            'label' => 'centros_trabajo',
            'module' => 'companies'
            ],
            ['name' => 'files',
            'slug' => 'files',
            'label' => 'archivos',
            'module' => 'companies'
            ],
            ['name' => 'customers',
            'slug' => 'customers',
            'label' => 'clientes',
            'module' => 'companies'
            ],
            ['name' => 'providers',
            'slug' => 'providers',
            'label' => 'proveedores',
            'module' => 'companies'
            ],

            ['name' => 'contracts',
            'slug' => 'contracts',
            'label' => 'contratos',
            'module' => 'rrhh'
            ],
            ['name' => 'employees',
            'slug' => 'employees',
            'label' => 'empleados',
            'module' => 'rrhh'
            ],
            ['name' => 'groups',
            'slug' => 'groups',
            'label' => 'grupos',
            'module' => 'rrhh'
            ],
            ['name' => 'staff pick',
            'slug' => 'staff-pick',
            'label' => 'seleccion_personal',
            'module' => 'rrhh'
            ],

            ['name' => 'products',
            'slug' => 'products',
            'label' => 'productos',
            'module' => 'products'
            ],
            ['name' => 'categories',
            'slug' => 'product-categories',
            'label' => 'categorias',
            'module' => 'products'
            ],
            ['name' => 'product-patterns',
            'slug' => 'product-patterns',
            'label' => 'productos_patrones',
            'module' => 'products'
            ],
            ['name' => 'parameters',
            'slug' => 'parameters',
            'label' => 'parametros',
            'module' => 'products'
            ],

            ['name' => 'stores',
            'slug' => 'stores',
            'label' => 'almacenes',
            'module' => 'stocks'
            ],
            ['name' => 'stock-movements',
            'slug' => 'stock-movements',
            'label' => 'stocks_movimientos_',
            'module' => 'stocks'
            ],
            ['name' => 'stocks',
            'slug' => 'stocks',
            'label' => 'stocks',
            'module' => 'stocks'
            ],
            ['name' => 'inventory',
            'slug' => 'inventory',
            'label' => 'inventario',
            'module' => 'stocks'
            ],

            ['name' => 'planning',
            'slug' => 'planning',
            'label' => 'planning',
            'module' => 'production'
            ],
            ['name' => 'work orders',
            'slug' => 'work-orders',
            'label' => 'trabajo_ordenes_',
            'module' => 'production'
            ],
            ['name' => 'work order patterns',
            'slug' => 'work-order-patterns',
            'label' => 'trabajo_ordenes_patrones',
            'module' => 'production'
            ],
            ['name' => 'batch patterns',
            'slug' => 'batch-patterns',
            'label' => 'lotes_patrones',
            'module' => 'production'
            ],

            ['name' => 'budgets',
            'slug' => 'budgets',
            'label' => 'presupuestos',
            'module' => 'budgets'
            ],
            ['name' => 'budgets from providers',
            'slug' => 'budgets-from-providers',
            'label' => 'presupuestos_proveedores',
            'module' => 'budgets'
            ],
            ['name' => 'concourse patterns',
            'slug' => 'concourse-patterns',
            'label' => 'concurso_patrones_',
            'module' => 'budgets'
            ],
            ['name' => 'budget forms',
            'slug' => 'budget-forms',
            'label' => 'formularios',
            'module' => 'budgets'
            ],
            ['name' => 'budget settings',
            'slug' => 'budget-settings',
            'label' => 'presupuestos_configuracion_',
            'module' => 'budgets'
            ],
            ['name' => 'budget patterns',
            'slug' => 'budget-patterns',
            'label' => 'presupuestos_patrones',
            'module' => 'budgets'
            ],

            ['name' => 'orders',
            'slug' => 'orders',
            'label' => 'pedidos',
            'module' => 'orders'
            ],
            ['name' => 'control panel',
            'slug' => 'control-panel',
            'label' => 'panel_control',
            'module' => 'orders'
            ],
            ['name' => 'order groups',
            'slug' => 'order-groups',
            'label' => 'pedidos_agrupacion_',
            'module' => 'orders'
            ],
            ['name' => 'order patterns',
            'slug' => 'order-patterns',
            'label' => 'patrones_venta',
            'module' => 'orders'
            ],
            ['name' => 'purchase patterns',
            'slug' => 'purchase-patterns',
            'label' => 'patrones_compra',
            'module' => 'orders'
            ],
            ['name' => 'order settings',
            'slug' => 'order-settings',
            'label' => 'pedidos_configuracion_',
            'module' => 'orders'
            ],
            
            ['name' => 'projects',
            'slug' => 'projects',
            'label' => 'proyectos',
            'module' => 'projects'
            ],
            ['name' => 'project patterns',
            'slug' => 'project-patterns',
            'label' => 'proyectos_patrones',
            'module' => 'projects'
            ],

            ['name' => 'deliveries',
            'slug' => 'deliveries',
            'label' => 'entregas',
            'module' => 'logistics'
            ],
            ['name' => 'receiving',
            'slug' => 'receiving',
            'label' => 'recepciones',
            'module' => 'logistics'
            ],
            ['name' => 'shipments',
            'slug' => 'shipments',
            'label' => 'envios',
            'module' => 'logistics'
            ],
            ['name' => 'delivery patterns',
            'slug' => 'delivery-patterns',
            'label' => 'patrones_entrega',
            'module' => 'logistics'
            ],
            ['name' => 'shipment patterns',
            'slug' => 'shipment-patterns',
            'label' => 'patrones_envio',
            'module' => 'logistics'
            ],
            ['name' => 'vehicles',
            'slug' => 'vehicles',
            'label' => 'vehiculos',
            'module' => 'logistics'
            ],

            ['name' => 'accounting',
            'slug' => 'accounting',
            'label' => 'contabilidad',
            'module' => 'accounting'            
            ],
            ['name' => 'invoices',
            'slug' => 'invoices',
            'label' => 'facturas',
            'module' => 'accounting'
            ],
            ['name' => 'expenses',
            'slug' => 'expenses',
            'label' => 'gastos',
            'module' => 'accounting'
            ],
            ['name' => 'invoice-patterns',
            'slug' => 'invoice-patterns',
            'label' => 'facturas_patrones_',
            'module' => 'accounting'
            ],
            ['name' => 'invoice settings',
            'slug' => 'invoice-settings',
            'label' => 'facturas_configuracion_',
            'module' => 'accounting'
            ],
            ['name' => 'bank accounts',
            'slug' => 'bank-accounts',
            'label' => 'bancos_cuentas',
            'module' => 'accounting'            
            ],

            ['name' => 'incidences',
            'slug' => 'incidences',
            'label' => 'incidencias',
            'module' => 'quality'
            ],

            ['name' => 'schedules',
            'slug' => 'schedules',
            'label' => 'agendas',
            'module' => 'schedule'
            ],
            ['name' => 'schedule event types',
            'slug' => 'schedule-event-types',
            'label' => 'eventos_tipo_',
            'module' => 'schedule'
            ],
            
            ['name' => 'work calendar',
            'slug' => 'work-calendar',
            'label' => 'calendario_laboral',
            'module' => 'calendar'
            ],
            ['name' => 'day types',
            'slug' => 'day-types',
            'label' => 'tipos_jornada',
            'module' => 'calendar'
            ],

            ['name' => 'employees timetable',
            'slug' => 'employees-timetable',
            'label' => 'horarios_empleados',
            'module' => 'timetables'
            ],
            ['name' => 'timetable-items',
            'slug' => 'timetable-items',
            'label' => 'horarios_conceptos_',
            'module' => 'timetables'
            ],
            ['name' => 'timetable signed',
            'slug' => 'timetable-signed',
            'label' => 'firma_horarios',
            'module' => 'timetables'
            ],

            ['name' => 'teams',
            'slug' => 'teams',
            'label' => 'equipos',
            'module' => 'teams'
            ],  

            ['name' => 'quizzes',
            'slug' => 'quizzes',
            'label' => 'concursos',
            'module' => 'quizzes'
            ], 
            ['name' => 'quiz editions',
            'slug' => 'quiz-editions',
            'label' => 'ediciones',
            'module' => 'quizzes'
            ],
            ['name' => 'questions',
            'slug' => 'questions',
            'label' => 'cuestiones',
            'module' => 'quizzes'
            ],

            ['name' => 'procedures',
            'slug' => 'procedures',
            'label' => 'procedimientos',
            'module' => 'procedures'
            ],
            ['name' => 'procedure patterns',
            'slug' => 'procedure-patterns',
            'label' => 'procedimientos_patrones',
            'module' => 'procedures'
            ]
        ];

        //Módulos:
        $modules = Module::all();

        foreach($data as $value){
            $module_id = false;

            //Pasamos el module_id al que pertenece la funcionalidad:
            foreach($modules as $module){
                if($module->slug == $value['module']){
                    $module_id = $module->id;
                    break;
                }
            }

            if($module_id){
                $func = new Functionality();
                $func->name = $value['name'];
                $func->slug = $value['slug'];
                $func->label = $value['label'];
                $func->module_id = $module_id;
                $func->save();
            }
        }    
    }
}
