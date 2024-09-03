<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LocaleMiddleware::class,
        ],

        'api' => [
            \App\Http\Middleware\EncryptCookies::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Session\Middleware\StartSession::class,
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        'iprestrict' => \App\Http\Middleware\RestrictIpMiddleware::class,
        'usersituation' => \App\Http\Middleware\UserSituationMiddleware::class,

        'accounting_concept_get' => \App\Http\Middleware\AccountingConceptGetMiddleware::class,

        'answer_get' => \App\Http\Middleware\AnswerGetMiddleware::class,
        'answer_post' => \App\Http\Middleware\AnswerPostMiddleware::class,

        'attribute_get' => \App\Http\Middleware\AttributeGetMiddleware::class,

        'bank_accounts_get' => \App\Http\Middleware\BankAccountsGetMiddleware::class,
        'bank_accounts_post' => \App\Http\Middleware\BankAccountsPostMiddleware::class,

        'batch_get' => \App\Http\Middleware\BatchGetMiddleware::class,

        'batch_pattern_get' => \App\Http\Middleware\BatchPatternGetMiddleware::class,

        'budget_get' => \App\Http\Middleware\BudgetGetMiddleware::class,
        'budget_post' => \App\Http\Middleware\BudgetPostMiddleware::class,

        'budget_form_get' => \App\Http\Middleware\BudgetFormGetMiddleware::class,
        'budget_form_post' => \App\Http\Middleware\BudgetFormPostMiddleware::class,

        'budget_form_item_get' => \App\Http\Middleware\BudgetFormItemGetMiddleware::class,

        'budget_order_get' => \App\Http\Middleware\BudgetOrderGetMiddleware::class,

        'budget_pattern_get' => \App\Http\Middleware\BudgetPatternGetMiddleware::class,

        'budget_settings_get' => \App\Http\Middleware\BudgetSettingsGetMiddleware::class,

        'business_area_get' => \App\Http\Middleware\BusinessAreaGetMiddleware::class,
        'business_area_post' => \App\Http\Middleware\BusinessAreaPostMiddleware::class,

        'cash_get' => \App\Http\Middleware\CashGetMiddleware::class,

        'category_get' => \App\Http\Middleware\CategoryGetMiddleware::class,

        'company_account' => \App\Http\Middleware\CompanyAccountMiddleware::class,
        'company_ajax' => \App\Http\Middleware\CompanyAjaxMiddleware::class,
        'company_get' => \App\Http\Middleware\CompanyGetMiddleware::class,
        'company_get3' => \App\Http\Middleware\CompanyGet3Middleware::class,
        'company_session' => \App\Http\Middleware\CompanySessionMiddleware::class,

        'company_utes_post' => \App\Http\Middleware\CompanyUtesPostMiddleware::class,

        'contract_get' => \App\Http\Middleware\ContractGetMiddleware::class,

        'cost_center_get' => \App\Http\Middleware\CostCenterGetMiddleware::class,
        'cost_center_post' => \App\Http\Middleware\CostCenterPostMiddleware::class,

        'customer_get' => \App\Http\Middleware\CustomerGetMiddleware::class,
        'customer_post' => \App\Http\Middleware\CustomerPostMiddleware::class,
        'customer_provider' => \App\Http\Middleware\CustomerProviderMiddleware::class,

        'day_types_get' => \App\Http\Middleware\DayTypeGetMiddleware::class,

        'dealer_get' => \App\Http\Middleware\DealerGetMiddleware::class,

        'delivery_get' => \App\Http\Middleware\DeliveryGetMiddleware::class,
        'delivery_post' => \App\Http\Middleware\DeliveryPostMiddleware::class,

        'employee_get' => \App\Http\Middleware\EmployeeGetMiddleware::class,

        'expense_get' => \App\Http\Middleware\ExpenseGetMiddleware::class,

        'group_get' => \App\Http\Middleware\GroupGetMiddleware::class,

        'invoice_customer_get' => \App\Http\Middleware\InvoiceCustomerGetMiddleware::class,
        'invoice_get' => \App\Http\Middleware\InvoiceGetMiddleware::class,
        'invoice_order_remove' => \App\Http\Middleware\InvoiceOrderRemoveMiddleware::class,
        'invoice_post' => \App\Http\Middleware\InvoicePostMiddleware::class,
        'invoice_update_get' => \App\Http\Middleware\InvoiceUpdateGetMiddleware::class,

        'invoice_modality' => \App\Http\Middleware\InvoiceModalityMiddleware::class,

        'invoice_pattern_get' => \App\Http\Middleware\InvoicePatternGetMiddleware::class,

        'ip_access_get' => \App\Http\Middleware\IpAccessGetMiddleware::class,

        'ip_addresses' => \App\Http\Middleware\IpAddressesMiddleware::class,

        'item_get' => \App\Http\Middleware\ItemGetMiddleware::class,

        'management_customers' => \App\Http\Middleware\ManagementCustomersMiddleware::class,
        'management_providers' => \App\Http\Middleware\ManagementProvidersMiddleware::class,

        'module_setted' => \App\Http\Middleware\ModuleSettedMiddleware::class,

        'order_get' => \App\Http\Middleware\OrderGetMiddleware::class,
        'order_post' => \App\Http\Middleware\OrderPostMiddleware::class,

        'order_groups_get' => \App\Http\Middleware\OrderGroupsGetMiddleware::class,
        'order_groups_post' => \App\Http\Middleware\OrderGroupsPostMiddleware::class,

        'order_pattern_get' => \App\Http\Middleware\OrderPatternGetMiddleware::class,

        'payment_methods_get' => \App\Http\Middleware\PaymentMethodsGetMiddleware::class,

        'phone_get' => \App\Http\Middleware\PhoneGetMiddleware::class,

        'procedure_get' => \App\Http\Middleware\ProcedureGetMiddleware::class,

        'procedure_pattern_get' => \App\Http\Middleware\ProcedurePatternGetMiddleware::class,

        'product_get' => \App\Http\Middleware\ProductGetMiddleware::class,
        'product_post' => \App\Http\Middleware\ProductPostMiddleware::class,

        'product_pattern_get' => \App\Http\Middleware\ProductPatternGetMiddleware::class,

        'project_get' => \App\Http\Middleware\ProjectGetMiddleware::class,
        'project_post' => \App\Http\Middleware\ProjectPostMiddleware::class,

        'project_pattern_get' => \App\Http\Middleware\ProjectPatternGetMiddleware::class,

        'project_purchases' => \App\Http\Middleware\ProjectPurchasesMiddleware::class,

        'protocol_get' => \App\Http\Middleware\ProtocolGetMiddleware::class,

        'purchase_pattern_get' => \App\Http\Middleware\PurchasePatternGetMiddleware::class,

        'question_get' => \App\Http\Middleware\QuestionGetMiddleware::class,

        'quiz_edition_get' => \App\Http\Middleware\QuizEditionGetMiddleware::class,
        'quiz_edition_post' => \App\Http\Middleware\QuizEditionPostMiddleware::class,
        'quiz_edition_show' => \App\Http\Middleware\QuizEditionShowMiddleware::class,

        'quiz_edition_team_get' => \App\Http\Middleware\QuizEditionTeamGetMiddleware::class,

        'quiz_get' => \App\Http\Middleware\QuizGetMiddleware::class,

        'receiving_get' => \App\Http\Middleware\ReceivingGetMiddleware::class,

        'remittance_get' => \App\Http\Middleware\RemittanceGetMiddleware::class,

        'remittance_pattern_get' => \App\Http\Middleware\RemittancePatternGetMiddleware::class,

        'role_company' => \App\Http\Middleware\RoleCompanyMiddleware::class,
        'role_get' => \App\Http\Middleware\RoleGetMiddleware::class,

        'sat_pattern_get' => \App\Http\Middleware\SatPatternGetMiddleware::class,

        'seat_get' => \App\Http\Middleware\SeatGetMiddleware::class,

        'seat_model_get' => \App\Http\Middleware\SeatModelGetMiddleware::class,

        'schedule_event_type_get' => \App\Http\Middleware\ScheduleEventTypeGetMiddleware::class,

        'schedule_get' => \App\Http\Middleware\ScheduleGetMiddleware::class,

        'shipment_pattern_get' => \App\Http\Middleware\ShipmentPatternGetMiddleware::class,

        'staff_pick_candidates_get' => \App\Http\Middleware\StaffPickCandidatesGetMiddleware::class,
        'staff_pick_candidates_post' => \App\Http\Middleware\StaffPickCandidatesPostMiddleware::class,

        'staff_pick_get' => \App\Http\Middleware\StaffPickGetMiddleware::class,

        'stock_movement_get' => \App\Http\Middleware\StockMovementGetMiddleware::class,

        'store_get' => \App\Http\Middleware\StoreGetMiddleware::class,

        'super_admin_validate' => \App\Http\Middleware\SuperAdminValidateMiddleware::class,

        'task_get' => \App\Http\Middleware\TaskGetMiddleware::class,
        'task_post' => \App\Http\Middleware\TaskPostMiddleware::class,

        'team_get' => \App\Http\Middleware\TeamGetMiddleware::class,
        'team_post' => \App\Http\Middleware\TeamPostMiddleware::class,

        'user_companies_get' => \App\Http\Middleware\UserCompaniesGetMiddleware::class,
        'user_companies_post' => \App\Http\Middleware\UserCompaniesPostMiddleware::class,
        'user_companies_post2' => \App\Http\Middleware\UserCompaniesPost2Middleware::class,

        'user_get' => \App\Http\Middleware\UserGetMiddleware::class,
        'user_post' => \App\Http\Middleware\UserPostMiddleware::class,

        'user_recommendation_get' => \App\Http\Middleware\UserRecommendationGetMiddleware::class,

        'vehicle_get' => \App\Http\Middleware\VehicleGetMiddleware::class,

        'work_order_component_get' => \App\Http\Middleware\WorkOrderComponentGetMiddleware::class,

        'work_order_get' => \App\Http\Middleware\WorkOrderGetMiddleware::class,
        'work_order_post' => \App\Http\Middleware\WorkOrderPostMiddleware::class,

        'work_order_pattern_get' => \App\Http\Middleware\WorkOrderPatternGetMiddleware::class,

        'work_order_product_get' => \App\Http\Middleware\WorkOrderProductGetMiddleware::class,
        'work_order_product_post' => \App\Http\Middleware\WorkOrderProductPostMiddleware::class,

        'workplace_get' => \App\Http\Middleware\WorkplaceGetMiddleware::class,
        'workplace_post' => \App\Http\Middleware\WorkplacePostMiddleware::class,

        //Spatie Role & Permissions:
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ];
}
