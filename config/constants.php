<?php 

return [
    'ACCOUNT_DURATION_' => 365,
    'ACCOUNT_DEFAULT_SALES_' => 10,
    'ACCOUNT_DEFAULT_SALES_IVA_' => 20,
    'ACCOUNT_DEFAULT_PURCHASES_' => 30,
    'ACCOUNT_DEFAULT_PURCHASES_IVA_' => 40,
    'ACCOUNT_DEFAULT_CASH_SALES_' => 50,
    'ACCOUNT_DEFAULT_CASH_PURCHASES_' => 60,
    'ACCOUNTING_ACCOUNT_DIGITS_DEFAULT_' => 4,
    'ACCOUNTING_METHOD_DEFAULT_' => 2,      //Método contable por defecto.
    'ACCOUNTING_METHODS_INVOICE_' => [1,8],   //Métodos contables a ejecutar en facturas.
    'ACCOUNTING_METHODS_PAYMENT_' => [2],   //Métodos contables a ejecutar en pagos.
    'ANSWER_TYPE_BOOLEAN_' => 10,
    'ANSWER_TYPE_BY_POINTS_' => 20,
    'BASIC_TIMETABLE_ITEMS_' => false,      //Marca si se pueden utilizar las opciones básicas de fichajes para móvil.
    'BUDGET_GENERIC_BLOCK_' => 'bloque',
    'BUDGET_ITEM_ID_DEFAULT_' => 1,
    'BUDGET_BASIC_MODALITY_' => 1,
    'BUDGET_FORM_MODALITY_' => 10,
    'BUDGET_CATALOGUE_MODALITY_' => 20,
    'BUDGET_STATUS_APPROVED_' => [20],
    'BUDGET_STATUS_APPROVED__' => 20,
    'BUDGET_STATUS_CANCELLED_' => 30,
    'BUDGET_STATUS_CLOSED_' => 40,
    'BUDGET_STATUS_INITIAL_' => 1,
    'CHAT_ROLE_ADMIN_' => 10,
    'CHAT_ROLE_USER_' => 1,
    'COLOR_PRIMARY_' => '#23c6c8',
    'COLOR_DANGER_' => '#ed5565',
    'COLOR_INFO_' => '#19ABD4',
    'COLOR_SUCCESS_' => '#18a689',
    'COLOR_WARNING_' => '#f8ac59',
    'COLOR_CHART_SUCCESS_' => "#a3e1d4",
    'COLOR_CHART_WARNING_' => "#e1d4a3",
    'COLOR_CHART_DANGER_' => "#f16688",
    'COLOR_CHART_GREY_' => "#dedede",
    'COMPONENTS_PRODUCTION_' => [1, 10],
    'CONTRATO_INDEFINIDO_DURACION_' => 100000,
    'CURRENCY_' => '€',
    'CURRENCY_NAME_' => 'EUR',
    'DATE_NULL_' => '0000-00-00',
    'DELIVERY_STATUS_PREPARATION_' => 1,
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
    'JOKE_' => false,
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
    'OMNI_USERS_' => [1,171,176,177,616],
    'ORDER_FINISHED_' => 70,
    'ORDER_GROUPS_DEFAULT_' => 10,
    'ORDER_GROUPS_OPENED_' => [10],

    'ORDER_STATUS_FINISHED_' => [40, 70],
    'ORDER_STATUS_INVOICED_' => [10, 40, 70],           //Status de pedidos facturables. 
    'ORDER_STATUS_NOT_DELETED_' => [15, 40, 50, 70],    //Status de pedidos no eliminables.
    'ORDER_STATUS_NOT_INVOICED_' => [30],               //Status de pedidos no facturables.
    'ORDER_STATUS_PENDENT_' => 1,
    'ORDER_STATUS_APPROVED_' => 10,
    'ORDER_STATUS_WORKING_' => 20,
    'ORDER_STATUS_PERCENTAGE_' => 25,
    'ORDER_STATUS_CANCELLED_' => 30,
    'ORDER_STATUS_DELIVERED_' => 40,
    'ORDER_STATUS_INCIDENCE_' => 50,
    'ORDER_STATUS_WITH_INVOICE_' => 70,

    'OTHER_PERMISSIONS_' => ['work-user-calendar'],     //Añadimos aquí los permisos sin funcionalidad asociada.
    'PERMISSIONS_OFF_' => ['accounts', 'modules', 'permissions', 'currencies', 'banks', 'countries', 'contents', 'units', 'stock-movements'],                  //Permisos exclusivos para los Super Admin.
    'PROTOCOL_STATUS_ACTIVE_' => 10,
    'PROTOCOL_STATUS_DRAFT_' => 1,
    'PROTOCOL_STATUS_INACTIVE_' => 20,
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
    'TOLERANCE_INVOICED_' => 99.99,    //Indica el valor de tolerancia admitido para facturas.
    'UNDEFINED_DATE_' => '9999-12-31',
    
];
?>

