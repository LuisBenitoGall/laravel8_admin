<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyWorkplacesController;
use App\Http\Controllers\Api\CountryProvincesController;
use App\Http\Controllers\Api\ProvinceTownsController;
use App\Http\Controllers\Api\UserWorkplacesController;

use App\Http\Controllers\Api\WorkplaceUsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// ADMIN DATATABLES:
Route::middleware(['auth', 'admin', 'iprestrict'])->prefix('admin')->namespace('Admin')->group(function () {
    //Accounts:
    Route::get('accounts', 'AccountController@getAccountsDT');

    //Attributes:
    Route::get('attributes', 'AttributeController@getAttributesDT');

    //Banks:
    Route::get('banks', 'BankController@getBanksDT');

    //BatchPatterns:
    Route::get('batch-patterns', 'BatchPatternsController@getPatternsDT');

    //BudgetFormCustomers:
    Route::get('budget-form-customers/{form}', 'BudgetFormsController@getBudgetFormCustomersDT');

    //BudgetFormItems:
    Route::get('budget-form-items/{form}/{recid?}', 'BudgetFormItemsController@getBudgetFormItemsDT');

    //BudgetForms:
    Route::get('budget-forms', 'BudgetFormsController@getBudgetFormsDT');

    //BudgetPatterns:
    Route::get('budget-patterns', 'BudgetPatternsController@getPatternsDT');

    //Budgets:
    Route::get('budgets/{from}', 'BudgetController@getBudgetsDT');

    //BudgetSettings:
    Route::get('budget-settings', 'BudgetSettingsController@getTemplatesDT');

    //BusinessAreas:
    Route::get('business-areas/{company}/{business_relationship}', 'BusinessAreasController@getBusinessAreasDT');

    //Categories:
    Route::get('categories/{module_id}', 'CategoryController@getCategoriesDT');

    //Companies:
    Route::get('companies', 'CompanyController@getCompaniesDT');

    //CompanyAccounts:
    Route::get('company-accounts', 'CompanyAccountsController@getCompanyAccountsDT');

    //CompanySectors:
    Route::get('company-sectors/{company_id}', 'CompanySectorsController@getCompanySectorsDT');

    //Contents:
    Route::get('contents', 'ContentController@getContentsDT');

    //Contracts:
    Route::get('contracts', 'ContractController@getContractsDT');

    //CostCenters:
    Route::get('cost-centers/{company}/{business_relationship}', 'CostCentersController@getCostCentersDT');

    //Countries:
    Route::get('countries', 'CountryController@getCountriesDT');

    //Currencies:
    Route::get('currencies', 'CurrencyController@getCurrenciesDT');

    //CustomerProducts:
    Route::get('customer-products/{customer_id}/{ba_id?}/{cc_id?}', 'CustomerProductsController@getProductsDT');

    //Customers:
    Route::get('customers', 'CustomerProvidersController@getCustomersDT');

    //Deliveries:
    Route::get('deliveries', 'DeliveryController@getDeliveriesDT');

    //DeliveriesOrders:
    Route::get('deliveries-orders', 'DeliveryController@getDeliveriesOrdersDT');

    //DeliveryPatterns:
    Route::get('delivery-patterns', 'DeliveryPatternsController@getPatternsDT');

    //DayTypes:
    Route::get('day-types', 'DayTypesController@getDayTypesDT');

    //Employees:
    Route::get('employees', 'EmployeeController@getEmployeesDT');

    //EmployeesTimetables:
    Route::get('employees-timetable/{workplace_id}/{from}/{to}', 'TimetableController@getEmployeesTimetablesDT');

    //Expenses:
    Route::get('expenses', 'ExpenseController@getExpensesDT');

    //GroupMembers:
    Route::get('group-members/{group_id}', 'GroupMembersController@getGroupMembersDT');

    //Groups:
    Route::get('groups', 'GroupController@getGroupsDT');

    //InvoicePatterns:
    Route::get('invoice-patterns', 'InvoicePatternsController@getPatternsDT');

    //Invoices:
    Route::get('invoices/{year?}/{customer?}/{provider?}', 'InvoiceController@getInvoicesDT');

    //Invoice Settings:
    Route::get('invoice-settings', 'InvoiceSettingsController@getTemplatesDT');

    //IpAccess:
    Route::get('ip-access', 'IpAccessController@getIpsDT');

    //Modules:
    Route::get('modules', 'ModuleController@getModulesDT');

    //Notifications:
    Route::get('notifications-read', 'NotificationController@getReadDT');
    Route::get('notifications-unread', 'NotificationController@getUnreadDT');

    //OrderGroups:
    Route::get('order-groups/{customer}/{year}', 'OrderGroupsController@getGroupsDT');

    //OrderItems:
    Route::get('order-items/{order}/{environment}', 'OrderController@getItemsDT');

    //OrderPatterns:
    Route::get('order-patterns', 'OrderPatternsController@getPatternsDT');

    //OrderSettings:
    Route::get('order-settings', 'OrderSettingsController@getTemplatesDT');

    //OrderTeams:
    Route::get('order-teams/{order?}/{environment?}', 'OrderController@getTeamDT');

    //Orders:
    Route::get('orders/{year?}/{customer?}/{provider?}/{owner?}/{status?}/{order_buy?}', 'OrderController@getOrdersDT');

    //Orders Control Panel Purchases:
    Route::get('orders-control-panel-purchases/{year}', 'OrderController@getOrdersControlPanelPurchasesDT');

    //Orders Control Panel Sales:
    Route::get('orders-control-panel-sales/{year}', 'OrderController@getOrdersControlPanelSalesDT');

    //Permissions:
    Route::get('permissions', 'PermissionController@getPermissionsDT');

    //ProcedurePatterns:
    Route::get('procedure-patterns', 'ProcedurePatternsController@getPatternsDT');

    //Procedures:
    Route::get('procedures', 'ProcedureController@getProceduresDT');

    //ProductAttributes:
    Route::get('product-attributes/{product_id}', 'ProductAttributesController@getAttributesDT');

    //ProductCategories:
    Route::get('product-categories/{product_id}', 'ProductCategoriesController@getCategoriesDT');

    //ProductPatterns:
    Route::get('product-patterns', 'ProductPatternsController@getPatternsDT');

    //ProductProviders:
    Route::get('product-providers/{product_id}', 'ProductProvidersController@getProvidersDT');

    //ProductProviders:
    Route::get('product-providers-correspondences/{product_id}/{provider_id}', 'ProductProvidersController@getProvidersCorrespondencesDT');

    //ProductSerials:
    Route::get('product-serials/{product_id}', 'ProductSerialsController@getSerialsDT');

    //ProductSerialsCards:
    Route::get('product-serials-cards/{serial_id}', 'ProductSerialsCardsController@getProductAttributesDT');

    //ProductUnits:
    Route::get('product-units/{product_id}', 'ProductUnitsController@getUnitsDT');

    //Products:
    Route::get('products/{company_id?}', 'ProductController@getProductsDT');

    //ProjectExpenses:
    Route::get('project-expenses/{project_id}', 'ProjectController@getProjectExpensesDT');

    //ProjectPatterns:
    Route::get('project-patterns', 'ProjectPatternsController@getPatternsDT');

    //Projects:
    Route::get('projects/{status?}', 'ProjectController@getProjectsDT');

    //ProviderProducts:
    Route::get('provider-products/{provider_id}', 'CustomerProductsController@getProviderProductsDT');

    //Providers:
    Route::get('providers', 'CustomerProvidersController@getProvidersDT');

    //Provinces:
    Route::get('provinces/{country_id}', 'ProvinceController@getProvincesDT');

    //PurchasePatterns:
    Route::get('purchase-patterns', 'PurchasePatternsController@getPatternsDT');

    //QuestionAnswers:
    Route::get('question-answers/{question}', 'AnswerController@getQuestionAnswersDT');

    //Questions:
    Route::get('questions', 'QuestionController@getQuestionsDT');

    //QuizEditionQuestions:
    Route::get('quiz-edition-questions/{edition}', 'QuizEditionQuestionsController@getQuizEditionQuestionsDT');

    //QuizEditions:
    Route::get('quiz-editions', 'QuizEditionsController@getQuizEditionsDT');

    //QuizEditionTeams:
    Route::get('quiz-edition-teams/{edition}', 'QuizEditionTeamsController@getQuizEditionTeamsDT');

    //Quizzes:
    Route::get('quizzes', 'QuizController@getQuizzesDT');

    //Receivings:
    Route::get('receivings', 'ReceivingController@getReceivingsDT');

    //Roles:
    Route::get('roles', 'RoleController@getRolesDT');

    //ScheduleEventTypes:
    Route::get('schedule-event-types', 'ScheduleEventTypesController@getEventTypesDT');

    //ShipmentPatterns:
    Route::get('shipment-patterns', 'ShipmentPatternsController@getPatternsDT');

    //StaffPickCandidates:
    Route::get('staff-pick-candidates/{process}', 'StaffPickCandidatesController@getCandidatesDT');

    //StaffPicks:
    Route::get('staff-picks', 'StaffPickController@getProcessesDT');

    //StockMovements:
    Route::get('stock-movements', 'StockMovementsController@getMovementsDT');

    //Stores:
    Route::get('stores', 'StoreController@getStoresDT');

    //Teams:
    Route::get('teams', 'TeamController@getTeamsDT');

    //TimetableItems:
    Route::get('timetable-items', 'TimetableItemsController@getTimetableItemsDT');

    //Towns:
    Route::get('towns/{province_id}', 'TownController@getTownsDT');

    //Units:
    Route::get('units', 'UnitController@getUnitsDT');

    //UserCategories:
    Route::get('user-categories', 'UserCategoriesController@getUserCategoriesDT');

    //UserCompanies:
    Route::get('user-companies/{user}', 'UserCompaniesController@getCompaniesDT');

    //Users:
    Route::get('users/{all?}', 'UserController@getUsersDT');

    //Users All:
    //Route::get('users-all', 'UserController@getUsersAllDT');

    //UserTimetables:
    Route::get('user-timetables/{from}/{to}/{user}', 'UserController@userTimetablesDT');

    //Vehicles:
    Route::get('vehicles', 'VehicleController@getVehiclesDT');

    //Work Order Patterns:
    Route::get('work-order-patterns', 'WorkOrderPatternsController@getPatternsDT');

    //Work Order Products:
    Route::get('work-order-products/{work_order}', 'WorkOrderProductsController@getProductsDT');

    //Work Orders:
    Route::get('work-orders', 'WorkOrderController@getWorkOrdersDT');

    //Workplaces:
    Route::get('workplaces/{company}/{business_relationship}', 'WorkplaceController@getWorkplacesDT');
});


Route::prefix('v1')->namespace('Api')->group(function () {



    Route::name('api.')
        ->middleware('auth:api')
        ->group(function () {
            Route::apiResource('companies', CompanyController::class);

            // Company Workplaces
            Route::get('/companies/{company}/workplaces', [
                CompanyWorkplacesController::class,
                'index',
            ])->name('companies.workplaces.index');
            Route::post('/companies/{company}/workplaces', [
                CompanyWorkplacesController::class,
                'store',
            ])->name('companies.workplaces.store');

            Route::apiResource('countries', CountryController::class);

            // Country Provinces
            Route::get('/countries/{country}/provinces', [
                CountryProvincesController::class,
                'index',
            ])->name('countries.provinces.index');
            Route::post('/countries/{country}/provinces', [
                CountryProvincesController::class,
                'store',
            ])->name('countries.provinces.store');

            Route::apiResource('provinces', ProvinceController::class);

            // Province Towns
            Route::get('/provinces/{province}/towns', [
                ProvinceTownsController::class,
                'index',
            ])->name('provinces.towns.index');
            Route::post('/provinces/{province}/towns', [
                ProvinceTownsController::class,
                'store',
            ])->name('provinces.towns.store');

            Route::apiResource('towns', TownController::class);


            // User Workplaces
            Route::get('/users/{user}/workplaces', [
                UserWorkplacesController::class,
                'index',
            ])->name('users.workplaces.index');
            Route::post('/users/{user}/workplaces/{workplace}', [
                UserWorkplacesController::class,
                'store',
            ])->name('users.workplaces.store');
            Route::delete('/users/{user}/workplaces/{workplace}', [
                UserWorkplacesController::class,
                'destroy',
            ])->name('users.workplaces.destroy');

            Route::apiResource('workplaces', WorkplaceController::class);

            // Workplace Users
            Route::get('/workplaces/{workplace}/users', [
                WorkplaceUsersController::class,
                'index',
            ])->name('workplaces.users.index');
            Route::post('/workplaces/{workplace}/users/{user}', [
                WorkplaceUsersController::class,
                'store',
            ])->name('workplaces.users.store');
            Route::delete('/workplaces/{workplace}/users/{user}', [
                WorkplaceUsersController::class,
                'destroy',
            ])->name('workplaces.users.destroy');
        });
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

});

