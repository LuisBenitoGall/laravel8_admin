<?php 

return [
    'ACCOUNT_DURATION_' => 365,
    'ANSWER_TYPE_BOOLEAN_' => 10,
    'ANSWER_TYPE_BY_POINTS_' => 20,
    'BASIC_TIMETABLE_ITEMS_' => true,      //Marca si se pueden utilizar las opciones básicas de fichajes para móvil.
    'BUDGET_GENERIC_BLOCK_' => 'bloque',
    'BUDGET_ITEM_ID_DEFAULT_' => 1,
    'BUDGET_BASIC_MODALITY_' => 1,
    'BUDGET_FORM_MODALITY_' => 10,
    'BUDGET_CATALOGUE_MODALITY_' => 20,
    'BUDGET_STATUS_APPROVED_' => [20],
    'BUDGET_STATUS_CANCELLED_' => 30,
    'BUDGET_STATUS_CLOSED_' => 40,
    'CHAT_ROLE_ADMIN_' => 10,
    'CHAT_ROLE_USER_' => 1,
    'CONTRATO_INDEFINIDO_DURACION_' => 100000,
    'CURRENCY_' => '€',
    'CURRENCY_NAME_' => 'EUR',
    'DATE_NULL_' => '0000-00-00',
    'EMAIL_' => 'lbenito@weareadn.com',
    'EMPLOYEE_ACTIVED_' => 1,
    'EMPLOYEE_EXPIRED_' => 2,
    'EMPLOYEE_INACTIVED_' => 0,
	'IDM_' => 'es',
    'INVOICE_OPEN_' => 1,   //Factura pendiente
    'INVOICE_PAID_' => 10, 
    'INVOICE_REFUNDED_' => 20,
    'INVOICE_STATUS_REFUNDED_' => [20],
    'IP_USER' => ['127.0.0.1'], // IP`s 
    'IVA_DEFAULT_' => 21,
    'METHODS_' => [
        ['index', 'Ver '], 
        ['create', 'Crear '], 
        ['show', 'Mostrar '], 
        ['search', 'Buscar '], 
        ['edit', 'Editar '], 
        ['update', 'Actualizar '],
        ['destroy', 'Eliminar ']
    ],
    'MIN_YEAR_' => 2018,
    'NOBATCH_' => 'lote_0',
    'NOTIFICATION_PERIOD_' => 30,
    'NUM_OF_YEARS_' => 10,           //valor para el cálculo de fechas durante un período de años.
    'OMNI_USERS_' => [1],
    'ORDER_FINISHED_' => 70,
    'ORDER_GROUPS_DEFAULT_' => 10,
    'ORDER_GROUPS_OPENED_' => [10],
    'ORDER_STATUS_INVOICED_' => [10, 40, 70],           //Status de pedidos facturables. 
    'ORDER_STATUS_NOT_DELETED_' => [15, 40, 50, 70],    //Status de pedidos no eliminables.
    'ORDER_STATUS_NOT_INVOICED_' => [30],               //Status de pedidos no facturables.
    'OTHER_PERMISSIONS_' => ['work-user-calendar'],     //Añadimos aquí los permisos sin funcionalidad asociada.
    'PERMISSIONS_OFF_' => ['accounts', 'modules', 'permissions', 'currencies', 'banks', 'countries', 'contents', 'units', 'stock-movements'],                  //Permisos exclusivos para los Super Admin.
    'QUESTION_CROSSWORD_' => 30,
    'QUESTION_FREE_TEXT_' => 20,
    'QUESTION_TEST_' => 10,
	'ROLE_' => 'usuario',			//role sin permisos.
    'ROLE_BASICO_' => 'basico',
	//'ROLE_ID_' => 3,				//id role sin permisos (extraído de la tabla 'roles').
    //'ROLE_COMPANY_MANAGER_' => 2,   //id role manager empresa (extraído de la tabla 'roles'). 
    'ROLE_SUPERADMIN_' => 1,        //id role con permisos absolutos (extraído de la tabla 'roles').
    'SET_ORDER_PREVISIONS_' => false,
    'SUPER_ADMIN_' => 'Super Admin',
    'UNDEFINED_DATE_' => '9999-12-31',
    
];
?>

