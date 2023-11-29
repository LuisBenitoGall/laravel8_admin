<?php

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\BankCompaniesController;
use App\Http\Controllers\Api\BudgetFormOrdersController;

use App\Http\Controllers\Api\BudgetPatternOrdersController;

use App\Http\Controllers\Api\BudgetSettingOrdersController;

use App\Http\Controllers\Api\BusinessAreaOrdersController;
use App\Http\Controllers\Api\CompanyAttributesController;
use App\Http\Controllers\Api\CompanyBanksController;
use App\Http\Controllers\Api\CompanyBatchPatternsController;
use App\Http\Controllers\Api\CompanyBudgetPatternsController;
use App\Http\Controllers\Api\CompanyBudgetSettingsController;
use App\Http\Controllers\Api\CompanyBusinessAreasController;

use App\Http\Controllers\Api\CompanyInvoicePatternsController;
use App\Http\Controllers\Api\CompanyInvoiceSettingsController;
use App\Http\Controllers\Api\CompanyOrderPatternsController;
use App\Http\Controllers\Api\CompanyOrdersController;
use App\Http\Controllers\Api\CompanyOrderSettingsController;
use App\Http\Controllers\Api\CompanyProductPatternsController;
use App\Http\Controllers\Api\CompanyProductsController;
use App\Http\Controllers\Api\CompanyPurchasePatternsController;
use App\Http\Controllers\Api\CompanyQuestionsController;
use App\Http\Controllers\Api\CompanyStaffPicksController;
use App\Http\Controllers\Api\CompanyWorkOrderPatternsController;

use App\Http\Controllers\Api\CompanyWorkOrdersController;
use App\Http\Controllers\Api\CompanyWorkplacesController;

use App\Http\Controllers\Api\CostCenterOrdersController;

use App\Http\Controllers\Api\CountryProvincesController;

use App\Http\Controllers\Api\InvoicePatternInvoicesController;
use App\Http\Controllers\Api\InvoiceSettingInvoicesController;
use App\Http\Controllers\Api\OrderInvoicesController;
use App\Http\Controllers\Api\OrderPatternOrdersController;

use App\Http\Controllers\Api\OrderSettingOrdersController;

use App\Http\Controllers\Api\ProvinceTownsController;
use App\Http\Controllers\Api\PurchasePatternOrdersController;

use App\Http\Controllers\Api\QuestionAnswersController;
use App\Http\Controllers\Api\UserInvoicesController;
use App\Http\Controllers\Api\UserOrdersController;
use App\Http\Controllers\Api\UserProductsController;
use App\Http\Controllers\Api\UserStaffPickCandidatesController;
use App\Http\Controllers\Api\UserStaffPicksController;
use App\Http\Controllers\Api\UserWorkOrdersController;
use App\Http\Controllers\Api\UserWorkplacesController;

use App\Http\Controllers\Api\WorkOrderBatchesController;
use App\Http\Controllers\Api\WorkOrderPatternWorkOrdersController;
use App\Http\Controllers\Api\WorkplaceOrdersController;
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

    //Accounting Account Types:
    Route::get('accounting-account-types', 'AccountingAccountTypeController@getAccountingAccountTypesDT');

    //Accounting Accounts:
    Route::get('accounting-accounts', 'AccountingAccountController@getAccountingAccountsDT');
    Route::post('accounting-accounts-search', 'AccountingAccountController@getAccounts');

    //AccountingConcepts:
    Route::get('accounting-concepts', 'AccountingConceptController@getConceptsDT');

    //Accounts:
    Route::get('accounts', 'AccountController@getAccountsDT');

    //Attributes:
    Route::get('attributes', 'AttributeController@getAttributesDT');

    //Banks:
    Route::get('banks', 'BankController@getBanksDT');

    //BatchPatterns:
    Route::get('batch-patterns', 'BatchPatternsController@getPatternsDT');

    //Batches:
    Route::get('batches/{product_id?}', 'BatchController@getBatchesDT');

    //BudgetFormCustomers:
    Route::get('budget-form-customers/{form}', 'BudgetFormsController@getBudgetFormCustomersDT');

    //BudgetFormItems:
    Route::get('budget-form-items/{form}/{recid?}', 'BudgetFormItemsController@getBudgetFormItemsDT');

    //BudgetForms:
    Route::get('budget-forms', 'BudgetFormsController@getBudgetFormsDT');

    //BudgetItems
    Route::get('budget-items/{order}', 'BudgetController@getItemsDT');

    //BudgetPatterns:
    Route::get('budget-patterns', 'BudgetPatternsController@getPatternsDT');

    //Budgets:
    Route::get('budgets/{from}', 'BudgetController@getBudgetsDT');

    //BudgetSettings:
    Route::get('budget-settings', 'BudgetSettingsController@getTemplatesDT');

    //BusinessAreas:
    Route::get('business-areas/{company}/{business_relationship}', 'BusinessAreasController@getBusinessAreasDT');

    //Cash:
    Route::get('cash', 'CashController@getCashDT');

    //Categories:
    Route::get('categories/{module_id}', 'CategoryController@getCategoriesDT');

    //Companies:
    Route::get('companies', 'CompanyController@getCompaniesDT');
    Route::post('companies-search', 'CompanyController@searchCompanies');

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

    //CustomerProvidersUsers:
    Route::get('customer-providers-users/{company_id}', 'CustomerProvidersController@getUsersDT');

    //Customers:
    Route::get('customers', 'CustomerProvidersController@getCustomersDT');

    //Deliveries:
    Route::get('deliveries/{year?}/{customer?}/{status?}', 'DeliveryController@getDeliveriesDT');

    //DeliveriesOrders:
    Route::get('deliveries-orders/{year?}/{customer?}', 'DeliveryController@getDeliveriesOrdersDT');

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

    //Inventory:
    Route::get('inventory', 'InventoryController@getInventoryDT');
    Route::get('inventory/{product_id}', 'InventoryController@getInventoryByProduct');

    //InvoiceControl:
     Route::get('invoice-control', 'InvoiceController@getControlDT');

    //InvoicePatterns:
    Route::get('invoice-patterns', 'InvoicePatternsController@getPatternsDT');

    //Invoices:
    Route::get('invoices/{year?}/{customer?}/{provider?}/{month?}', 'InvoiceController@getInvoicesDT');

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
    Route::get('orders/{year?}/{customer?}/{provider?}/{owner?}/{groups?}/{status?}/{from?}/{to?}/{order_buy?}', 'OrderController@getOrdersDT');
    Route::post('orders-search', 'OrderController@searchOrders');

    //Orders Control Panel Purchases:
    Route::get('orders-control-panel-purchases/{year}', 'OrderController@getOrdersControlPanelPurchasesDT');

    //Orders Control Panel Sales:
    Route::get('orders-control-panel-sales/{year}/{workplace_id?}', 'OrderController@getOrdersControlPanelSalesDT');

    //Payment Methods:
    Route::get('payment-methods', 'PaymentMethodController@getPaymentMethodsDT');

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

    //ProductPurchases:
    Route::get('product-purchases/{product_id}', 'ProductController@getPurchasesDT');

    //ProductSales:
    Route::get('product-sales/{product_id}', 'ProductController@getSalesDT');

    //ProductSerials:
    Route::get('product-serials/{product_id}', 'ProductSerialsController@getSerialsDT');

    //ProductSerialsCards:
    Route::get('product-serials-cards/{serial_id}', 'ProductSerialsCardsController@getProductAttributesDT');

    //ProductStockMovements:
    Route::get('product-stock-movements/{product_id}', 'StockController@getProductStockMovementsDT');

    //ProductUnits:
    Route::get('product-units/{product_id}', 'ProductUnitsController@getUnitsDT');

    //Products:
    Route::get('products/{company_id?}', 'ProductController@getProductsDT');
    Route::post('products-search', 'ProductController@searchProducts');

    //ProjectExpenses:
    Route::get('project-expenses/{project_id}', 'ProjectController@getProjectExpensesDT');

    //ProjectPatterns:
    Route::get('project-patterns', 'ProjectPatternsController@getPatternsDT');

    //Projects:
    Route::get('projects/{status?}', 'ProjectController@getProjectsDT');

    //Protocol Items:
    Route::get('protocol-items/{protocol_id}', 'ProtocolItemController@getProtocolItemsDT');

    //Protocol Users:
    Route::get('protocol-users/{protocol_id}', 'ProtocolUserController@getProtocolUsersDT');

    //Protocols:
    Route::get('protocols', 'ProtocolController@getProtocolsDT');

    //ProviderProducts:
    Route::get('provider-products/{provider_id}', 'CustomerProductsController@getProviderProductsDT');

    //Providers:
    Route::get('providers', 'CustomerProvidersController@getProvidersDT');
    Route::post('providers-search', 'CompanyController@searchProviders');

    //Provinces:
    Route::get('provinces/{country_id}', 'ProvinceController@getProvincesDT');

    //PurchasePatterns:
    Route::get('purchase-patterns', 'PurchasePatternsController@getPatternsDT');

    //QuestionAnswers:
    Route::get('question-answers/{question}', 'AnswerController@getQuestionAnswersDT');

    //Questions:
    Route::get('questions/{quiz_id}', 'QuestionController@getQuestionsDT');

    //QuizEditionCompanies:
    Route::get('quiz-edition-companies/{edition}', 'QuizEditionCompanyController@getQuizEditionCompaniesDT');

    //QuizEditionQuestions:
    Route::get('quiz-edition-questions/{edition}/{disabled?}', 'QuizEditionQuestionsController@getQuizEditionQuestionsDT');

    //QuizEditions:
    Route::get('quiz-editions', 'QuizEditionsController@getQuizEditionsDT');

    //QuizEditionTeams:
    Route::get('quiz-edition-teams/{edition}', 'QuizEditionTeamsController@getQuizEditionTeamsDT');

    //Quizzes:
    Route::get('quizzes', 'QuizController@getQuizzesDT');

    //Receivings:
    Route::get('receivings', 'ReceivingController@getReceivingsDT');

    //Recommendation Companies:
    Route::get('recommendation-companies', 'UserRecommendationController@getCompaniesDT');

    //Recommendations:
    Route::get('recommendations/{environment}', 'UserRecommendationController@getRecommendationsDT');

    //Recommendations Ranking:
    Route::get('recommendations-ranking/{item}', 'UserRecommendationController@getItemRankingDT');

    //Roles:
    Route::get('roles', 'RoleController@getRolesDT');

    //SAT:
    Route::get('sats', 'SatController@getSatsDT');

    //SATPatterns:
    Route::get('sat-patterns', 'SatPatternsController@getPatternsDT');

    //ScheduleEventTypes:
    Route::get('schedule-event-types', 'ScheduleEventTypesController@getEventTypesDT');

    //SeatLines:
    Route::get('seat-lines/{seat_id}', 'SeatLinesController@getLines');
    Route::get('seat-lines/{accounting_account_id}/dt/{from}/{to}', 'SeatLinesController@getLinesDT');

    //SeatModels:
    Route::get('seat-models', 'SeatModelController@getSeatModelsDT');

    //Seats:
    Route::get('seats/{year?}/{accountin_account_id?}/{from?}/{to?}', 'SeatController@getSeatsDT');

    //ShipmentPatterns:
    Route::get('shipment-patterns', 'ShipmentPatternsController@getPatternsDT');

    //Shipments:
    Route::get('shipments', 'ShipmentController@getShipmentsDT');

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
    Route::post('users-search', 'UserController@searchUsers');

    //Users All:
    //Route::get('users-all', 'UserController@getUsersAllDT');

    //UserTimetables:
    Route::get('user-timetables/{from}/{to}/{user}', 'UserController@userTimetablesDT');

    //Vehicles:
    Route::get('vehicles', 'VehicleController@getVehiclesDT');

    //Work Order Components:
    Route::get('work-order-components/{work_order}', 'WorkOrderComponentController@getComponentsDT');

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


    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::middleware('auth:api')
        ->get('/user', function (Request $request) {
            $user = collect($request->user());
            return $user->merge(['img_company' => asset('img/companies/' . $request->user()->companies()[0]->logo)]);

        })
        ->name('api.user');

    Route::name('api.')
        ->middleware('auth:api')
        ->group(function () {
            Route::apiResource('budget-forms', BudgetFormController::class);

            // BudgetForm Orders
            Route::get('/budget-forms/{budgetForm}/orders', [
                BudgetFormOrdersController::class,
                'index',
            ])->name('budget-forms.orders.index');
            Route::post('/budget-forms/{budgetForm}/orders', [
                BudgetFormOrdersController::class,
                'store',
            ])->name('budget-forms.orders.store');

            Route::apiResource('budget-patterns', BudgetPatternController::class);

            // BudgetPattern Orders
            Route::get('/budget-patterns/{budgetPattern}/orders', [
                BudgetPatternOrdersController::class,
                'index',
            ])->name('budget-patterns.orders.index');
            Route::post('/budget-patterns/{budgetPattern}/orders', [
                BudgetPatternOrdersController::class,
                'store',
            ])->name('budget-patterns.orders.store');

            Route::apiResource('budget-settings', BudgetSettingController::class);

            // BudgetSetting Orders
            Route::get('/budget-settings/{budgetSetting}/orders', [
                BudgetSettingOrdersController::class,
                'index',
            ])->name('budget-settings.orders.index');
            Route::post('/budget-settings/{budgetSetting}/orders', [
                BudgetSettingOrdersController::class,
                'store',
            ])->name('budget-settings.orders.store');

            Route::apiResource('business-areas', BusinessAreaController::class);

            // BusinessArea Orders
            Route::get('/business-areas/{businessArea}/orders', [
                BusinessAreaOrdersController::class,
                'index',
            ])->name('business-areas.orders.index');
            Route::post('/business-areas/{businessArea}/orders', [
                BusinessAreaOrdersController::class,
                'store',
            ])->name('business-areas.orders.store');

            // BusinessArea Provider Business Area
            Route::get('/business-areas/{businessArea}/orders', [
                BusinessAreaOrdersController::class,
                'index',
            ])->name('business-areas.orders.index');
            Route::post('/business-areas/{businessArea}/orders', [
                BusinessAreaOrdersController::class,
                'store',
            ])->name('business-areas.orders.store');

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

            // Company Orders
            Route::get('/companies/{company}/orders', [
                CompanyOrdersController::class,
                'index',
            ])->name('companies.orders.index');
            Route::post('/companies/{company}/orders', [
                CompanyOrdersController::class,
                'store',
            ])->name('companies.orders.store');

            // Company Order Settings
            Route::get('/companies/{company}/order-settings', [
                CompanyOrderSettingsController::class,
                'index',
            ])->name('companies.order-settings.index');
            Route::post('/companies/{company}/order-settings', [
                CompanyOrderSettingsController::class,
                'store',
            ])->name('companies.order-settings.store');

            // Company Budget Settings
            Route::get('/companies/{company}/budget-settings', [
                CompanyBudgetSettingsController::class,
                'index',
            ])->name('companies.budget-settings.index');
            Route::post('/companies/{company}/budget-settings', [
                CompanyBudgetSettingsController::class,
                'store',
            ])->name('companies.budget-settings.store');

            // Company Budget Patterns
            Route::get('/companies/{company}/budget-patterns', [
                CompanyBudgetPatternsController::class,
                'index',
            ])->name('companies.budget-patterns.index');
            Route::post('/companies/{company}/budget-patterns', [
                CompanyBudgetPatternsController::class,
                'store',
            ])->name('companies.budget-patterns.store');

            // Company Business Areas
            Route::get('/companies/{company}/business-areas', [
                CompanyBusinessAreasController::class,
                'index',
            ])->name('companies.business-areas.index');
            Route::post('/companies/{company}/business-areas', [
                CompanyBusinessAreasController::class,
                'store',
            ])->name('companies.business-areas.store');

            // Company Order Patterns
            Route::get('/companies/{company}/order-patterns', [
                CompanyOrderPatternsController::class,
                'index',
            ])->name('companies.order-patterns.index');
            Route::post('/companies/{company}/order-patterns', [
                CompanyOrderPatternsController::class,
                'store',
            ])->name('companies.order-patterns.store');

            // Company Purchase Patterns
            Route::get('/companies/{company}/purchase-patterns', [
                CompanyPurchasePatternsController::class,
                'index',
            ])->name('companies.purchase-patterns.index');
            Route::post('/companies/{company}/purchase-patterns', [
                CompanyPurchasePatternsController::class,
                'store',
            ])->name('companies.purchase-patterns.store');

            // Company Provider
            Route::get('/companies/{company}/orders', [
                CompanyOrdersController::class,
                'index',
            ])->name('companies.orders.index');
            Route::post('/companies/{company}/orders', [
                CompanyOrdersController::class,
                'store',
            ])->name('companies.orders.store');

            // Company Final Customer
            Route::get('/companies/{company}/orders', [
                CompanyOrdersController::class,
                'index',
            ])->name('companies.orders.index');
            Route::post('/companies/{company}/orders', [
                CompanyOrdersController::class,
                'store',
            ])->name('companies.orders.store');

            // Company Invoice Patterns
            Route::get('/companies/{company}/invoice-patterns', [
                CompanyInvoicePatternsController::class,
                'index',
            ])->name('companies.invoice-patterns.index');
            Route::post('/companies/{company}/invoice-patterns', [
                CompanyInvoicePatternsController::class,
                'store',
            ])->name('companies.invoice-patterns.store');

            // Company Invoice Settings
            Route::get('/companies/{company}/invoice-settings', [
                CompanyInvoiceSettingsController::class,
                'index',
            ])->name('companies.invoice-settings.index');
            Route::post('/companies/{company}/invoice-settings', [
                CompanyInvoiceSettingsController::class,
                'store',
            ])->name('companies.invoice-settings.store');

            // Company Questions
            Route::get('/companies/{company}/questions', [
                CompanyQuestionsController::class,
                'index',
            ])->name('companies.questions.index');
            Route::post('/companies/{company}/questions', [
                CompanyQuestionsController::class,
                'store',
            ])->name('companies.questions.store');

            // Company Attributes
            Route::get('/companies/{company}/attributes', [
                CompanyAttributesController::class,
                'index',
            ])->name('companies.attributes.index');
            Route::post('/companies/{company}/attributes', [
                CompanyAttributesController::class,
                'store',
            ])->name('companies.attributes.store');

            // Company Products
            Route::get('/companies/{company}/products', [
                CompanyProductsController::class,
                'index',
            ])->name('companies.products.index');
            Route::post('/companies/{company}/products', [
                CompanyProductsController::class,
                'store',
            ])->name('companies.products.store');

            // Company Product Patterns
            Route::get('/companies/{company}/product-patterns', [
                CompanyProductPatternsController::class,
                'index',
            ])->name('companies.product-patterns.index');
            Route::post('/companies/{company}/product-patterns', [
                CompanyProductPatternsController::class,
                'store',
            ])->name('companies.product-patterns.store');

            // Company Work Order Patterns
            Route::get('/companies/{company}/work-order-patterns', [
                CompanyWorkOrderPatternsController::class,
                'index',
            ])->name('companies.work-order-patterns.index');
            Route::post('/companies/{company}/work-order-patterns', [
                CompanyWorkOrderPatternsController::class,
                'store',
            ])->name('companies.work-order-patterns.store');

            // Company Work Orders
            Route::get('/companies/{company}/work-orders', [
                CompanyWorkOrdersController::class,
                'index',
            ])->name('companies.work-orders.index');
            Route::post('/companies/{company}/work-orders', [
                CompanyWorkOrdersController::class,
                'store',
            ])->name('companies.work-orders.store');

            // Company Batch Patterns
            Route::get('/companies/{company}/batch-patterns', [
                CompanyBatchPatternsController::class,
                'index',
            ])->name('companies.batch-patterns.index');
            Route::post('/companies/{company}/batch-patterns', [
                CompanyBatchPatternsController::class,
                'store',
            ])->name('companies.batch-patterns.store');

            // Company Banks
            Route::get('/companies/{company}/banks', [
                CompanyBanksController::class,
                'index',
            ])->name('companies.banks.index');
            Route::post('/companies/{company}/banks/{bank}', [
                CompanyBanksController::class,
                'store',
            ])->name('companies.banks.store');
            Route::delete('/companies/{company}/banks/{bank}', [
                CompanyBanksController::class,
                'destroy',
            ])->name('companies.banks.destroy');

            Route::apiResource('cost-centers', CostCenterController::class);

            // CostCenter Orders
            Route::get('/cost-centers/{costCenter}/orders', [
                CostCenterOrdersController::class,
                'index',
            ])->name('cost-centers.orders.index');
            Route::post('/cost-centers/{costCenter}/orders', [
                CostCenterOrdersController::class,
                'store',
            ])->name('cost-centers.orders.store');

            // CostCenter Provider Cost Center
            Route::get('/cost-centers/{costCenter}/orders', [
                CostCenterOrdersController::class,
                'index',
            ])->name('cost-centers.orders.index');
            Route::post('/cost-centers/{costCenter}/orders', [
                CostCenterOrdersController::class,
                'store',
            ])->name('cost-centers.orders.store');

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

            Route::apiResource('invoices', InvoiceController::class);

            Route::apiResource('invoice-patterns', InvoicePatternController::class);

            // InvoicePattern Invoices
            Route::get('/invoice-patterns/{invoicePattern}/invoices', [
                InvoicePatternInvoicesController::class,
                'index',
            ])->name('invoice-patterns.invoices.index');
            Route::post('/invoice-patterns/{invoicePattern}/invoices', [
                InvoicePatternInvoicesController::class,
                'store',
            ])->name('invoice-patterns.invoices.store');

            Route::apiResource('invoice-settings', InvoiceSettingController::class);

            // InvoiceSetting Invoices
            Route::get('/invoice-settings/{invoiceSetting}/invoices', [
                InvoiceSettingInvoicesController::class,
                'index',
            ])->name('invoice-settings.invoices.index');
            Route::post('/invoice-settings/{invoiceSetting}/invoices', [
                InvoiceSettingInvoicesController::class,
                'store',
            ])->name('invoice-settings.invoices.store');

            Route::apiResource('orders', OrderController::class);

            // Order Invoices
            Route::get('/orders/{order}/invoices', [
                OrderInvoicesController::class,
                'index',
            ])->name('orders.invoices.index');
            Route::post('/orders/{order}/invoices', [
                OrderInvoicesController::class,
                'store',
            ])->name('orders.invoices.store');

            Route::apiResource('order-patterns', OrderPatternController::class);

            // OrderPattern Orders
            Route::get('/order-patterns/{orderPattern}/orders', [
                OrderPatternOrdersController::class,
                'index',
            ])->name('order-patterns.orders.index');
            Route::post('/order-patterns/{orderPattern}/orders', [
                OrderPatternOrdersController::class,
                'store',
            ])->name('order-patterns.orders.store');

            Route::apiResource('order-settings', OrderSettingController::class);

            // OrderSetting Orders
            Route::get('/order-settings/{orderSetting}/orders', [
                OrderSettingOrdersController::class,
                'index',
            ])->name('order-settings.orders.index');
            Route::post('/order-settings/{orderSetting}/orders', [
                OrderSettingOrdersController::class,
                'store',
            ])->name('order-settings.orders.store');

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

            Route::apiResource(
                'purchase-patterns',
                PurchasePatternController::class
            );

            // PurchasePattern Orders
            Route::get('/purchase-patterns/{purchasePattern}/orders', [
                PurchasePatternOrdersController::class,
                'index',
            ])->name('purchase-patterns.orders.index');
            Route::post('/purchase-patterns/{purchasePattern}/orders', [
                PurchasePatternOrdersController::class,
                'store',
            ])->name('purchase-patterns.orders.store');

            Route::apiResource('towns', TownController::class);

            Route::apiResource('users', UserController::class);

            // User Orders
            Route::get('/users/{user}/orders', [
                UserOrdersController::class,
                'index',
            ])->name('users.orders.index');
            Route::post('/users/{user}/orders', [
                UserOrdersController::class,
                'store',
            ])->name('users.orders.store');

            // User Customer Manager
            Route::get('/users/{user}/orders', [
                UserOrdersController::class,
                'index',
            ])->name('users.orders.index');
            Route::post('/users/{user}/orders', [
                UserOrdersController::class,
                'store',
            ])->name('users.orders.store');

            // User Confirmed
            Route::get('/users/{user}/orders', [
                UserOrdersController::class,
                'index',
            ])->name('users.orders.index');
            Route::post('/users/{user}/orders', [
                UserOrdersController::class,
                'store',
            ])->name('users.orders.store');

            // User Provider Manager
            Route::get('/users/{user}/orders', [
                UserOrdersController::class,
                'index',
            ])->name('users.orders.index');
            Route::post('/users/{user}/orders', [
                UserOrdersController::class,
                'store',
            ])->name('users.orders.store');

            // User Final Customer Manager
            Route::get('/users/{user}/orders', [
                UserOrdersController::class,
                'index',
            ])->name('users.orders.index');
            Route::post('/users/{user}/orders', [
                UserOrdersController::class,
                'store',
            ])->name('users.orders.store');

            // User Created By
            Route::get('/users/{user}/orders', [
                UserOrdersController::class,
                'index',
            ])->name('users.orders.index');
            Route::post('/users/{user}/orders', [
                UserOrdersController::class,
                'store',
            ])->name('users.orders.store');

            // User Invoices
            Route::get('/users/{user}/invoices', [
                UserInvoicesController::class,
                'index',
            ])->name('users.invoices.index');
            Route::post('/users/{user}/invoices', [
                UserInvoicesController::class,
                'store',
            ])->name('users.invoices.store');

            // User Products
            Route::get('/users/{user}/products', [
                UserProductsController::class,
                'index',
            ])->name('users.products.index');
            Route::post('/users/{user}/products', [
                UserProductsController::class,
                'store',
            ])->name('users.products.store');

            // User Work Orders
            Route::get('/users/{user}/work-orders', [
                UserWorkOrdersController::class,
                'index',
            ])->name('users.work-orders.index');
            Route::post('/users/{user}/work-orders', [
                UserWorkOrdersController::class,
                'store',
            ])->name('users.work-orders.store');

            // User Work Orders2
            Route::get('/users/{user}/work-orders', [
                UserWorkOrdersController::class,
                'index',
            ])->name('users.work-orders.index');
            Route::post('/users/{user}/work-orders', [
                UserWorkOrdersController::class,
                'store',
            ])->name('users.work-orders.store');

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

            // Workplace Orders
            Route::get('/workplaces/{workplace}/orders', [
                WorkplaceOrdersController::class,
                'index',
            ])->name('workplaces.orders.index');
            Route::post('/workplaces/{workplace}/orders', [
                WorkplaceOrdersController::class,
                'store',
            ])->name('workplaces.orders.store');

            // Workplace Provider Workplace
            Route::get('/workplaces/{workplace}/orders', [
                WorkplaceOrdersController::class,
                'index',
            ])->name('workplaces.orders.index');
            Route::post('/workplaces/{workplace}/orders', [
                WorkplaceOrdersController::class,
                'store',
            ])->name('workplaces.orders.store');

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

            Route::apiResource('answers', AnswerController::class);

            Route::apiResource('questions', QuestionController::class);

            // Question Answers
            Route::get('/questions/{question}/answers', [
                QuestionAnswersController::class,
                'index',
            ])->name('questions.answers.index');
            Route::post('/questions/{question}/answers', [
                QuestionAnswersController::class,
                'store',
            ])->name('questions.answers.store');

            Route::apiResource('banks', BankController::class);

            // Bank Companies
            Route::get('/banks/{bank}/companies', [
                BankCompaniesController::class,
                'index',
            ])->name('banks.companies.index');
            Route::post('/banks/{bank}/companies/{company}', [
                BankCompaniesController::class,
                'store',
            ])->name('banks.companies.store');
            Route::delete('/banks/{bank}/companies/{company}', [
                BankCompaniesController::class,
                'destroy',
            ])->name('banks.companies.destroy');

            Route::apiResource('attributes', AttributeController::class);

            Route::apiResource('batches', BatchController::class);

            Route::apiResource('batch-patterns', BatchPatternController::class);

            Route::apiResource(
                'work-order-patterns',
                WorkOrderPatternController::class
            );

            // WorkOrderPattern Work Orders
            Route::get('/work-order-patterns/{workOrderPattern}/work-orders', [
                WorkOrderPatternWorkOrdersController::class,
                'index',
            ])->name('work-order-patterns.work-orders.index');
            Route::post('/work-order-patterns/{workOrderPattern}/work-orders', [
                WorkOrderPatternWorkOrdersController::class,
                'store',
            ])->name('work-order-patterns.work-orders.store');

            Route::apiResource('work-orders', WorkOrderController::class);

            // WorkOrder Batches
            Route::get('/work-orders/{workOrder}/batches', [
                WorkOrderBatchesController::class,
                'index',
            ])->name('work-orders.batches.index');
            Route::post('/work-orders/{workOrder}/batches', [
                WorkOrderBatchesController::class,
                'store',
            ])->name('work-orders.batches.store');

            // Device Push
            Route::apiResource('device-pushes', DevicePushController::class);

            // Company Staff Picks
            Route::get('/companies/{company}/staff-picks', [
                CompanyStaffPicksController::class,
                'index',
            ])->name('companies.staff-picks.index');

            Route::post('/companies/{company}/staff-picks', [
                CompanyStaffPicksController::class,
                'store',
            ])->name('companies.staff-picks.store');

            // User Staff Pick Candidates
            Route::get('/users/{user}/staff-pick-candidates', [
                UserStaffPickCandidatesController::class,
                'index',
            ])->name('users.staff-pick-candidates.index');
            Route::post('/users/{user}/staff-pick-candidates', [
                UserStaffPickCandidatesController::class,
                'store',
            ])->name('users.staff-pick-candidates.store');

            // User Staff Picks
            Route::get('/users/{user}/staff-picks', [
                UserStaffPicksController::class,
                'index',
            ])->name('users.staff-picks.index');
            Route::post('/users/{user}/staff-picks', [
                UserStaffPicksController::class,
                'store',
            ])->name('users.staff-picks.store');

            // User Staff Picks2
            Route::get('/users/{user}/staff-picks', [
                UserStaffPicksController::class,
                'index',
            ])->name('users.staff-picks.index');
            Route::post('/users/{user}/staff-picks', [
                UserStaffPicksController::class,
                'store',
            ])->name('users.staff-picks.store');

        });

    /**
     * device push
     */
    Route::apiResource('device-pushes', DevicePushController::class);


});

