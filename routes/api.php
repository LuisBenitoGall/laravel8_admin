<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

// ADMIN DATATABLES:
Route::middleware(['auth', 'admin', 'iprestrict'])->prefix('admin')->namespace('Admin')->group(function(){
    //Accounts:
    Route::get('accounts', 'AccountController@getAccountsDT');

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
    
    //Companies:
    Route::get('companies', 'CompanyController@getCompaniesDT');

    //CompanyAccounts:
    Route::get('company-accounts', 'CompanyAccountsController@getCompanyAccountsDT');
    
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
    Route::get('order-items/{shared}', 'OrderController@getItemsDT');

    //OrderPatterns:
    Route::get('order-patterns', 'OrderPatternsController@getPatternsDT');

    //OrderSettings:
    Route::get('order-settings', 'OrderSettingsController@getTemplatesDT');

    //OrderTeams:  
    Route::get('order-teams/{shared?}/{environment?}', 'OrderController@getTeamDT');

    //Orders:
    Route::get('orders/{customer?}/{provider?}/{owner?}/{status?}', 'OrderController@getOrdersDT');

    //Permissions:
    Route::get('permissions', 'PermissionController@getPermissionsDT');

    //ProcedurePatterns:
    Route::get('procedure-patterns', 'ProcedurePatternsController@getPatternsDT');

    //Procedures:
    Route::get('procedures', 'ProcedureController@getProceduresDT');

    //ProductPatterns:
    Route::get('product-patterns', 'ProductPatternsController@getPatternsDT');

    //ProductUnits:
    Route::get('product-units/{product_id}', 'ProductUnitsController@getUnitsDT');

    //Products:
    Route::get('products', 'ProductController@getProductsDT');

    //Projects:
    Route::get('projects/{status?}', 'ProjectController@getProjectsDT');

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

    //Roles:
    Route::get('roles', 'RoleController@getRolesDT');

    //ShipmentPatterns:
    Route::get('shipment-patterns', 'ShipmentPatternsController@getPatternsDT');

    //StaffPickCandidates:
    Route::get('staff-pick-candidates/{process}', 'StaffPickCandidatesController@getCandidatesDT');

    //StaffPicks:
    Route::get('staff-picks', 'StaffPickController@getProcessesDT');

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
    Route::get('users', 'UserController@getUsersDT');

    //Users All:
    Route::get('users-all', 'UserController@getUsersAllDT');

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

// API REST v1:
Route::prefix('v1')->namespace('Api_v1')->group(function(){
    //Companies:
    Route::get('/companies', 'CompanyController@index');

    //Users:
    Route::get('/users', 'UserController@index');

});