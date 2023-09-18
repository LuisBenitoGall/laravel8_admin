<?php

use Illuminate\Support\Facades\Route;
use Ladumor\OneSignal\OneSignal;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('test/push', function () {
foreach (\App\Models\DevicePush::where('onesignal',true)->get() as $item){

    $fields['include_player_ids'] = ["{$item->token}"];
    $notificationMsg = 'Prueba API SDK NOE ADN!';
    $msg = OneSignal::sendPush($fields, $notificationMsg);
    echo $item->token.' / '. $item->user->name;
}
});

Auth::routes();

// LANGUAGES:
Route::get('/lang/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
})->name('locale');
Route::get('string_translation/{id}/{schema}', 'LanguageController@stringTranslation');
Route::post('/languages/store_string', 'LanguageController@storeString')->name('languages.store_string');

// GENERICS:
Route::get('country_provinces/{id}', 'Admin\AdminController@getCountryProvinces');
Route::get('province-towns/{id}', 'Admin\AdminController@getProvinceTowns');

// FRONTEND:
Route::get('company-register', 'Admin\CompanyController@create')->name('company-register')->middleware('auth');
Route::post('company/store', 'Admin\CompanyController@store')->name('company.store')->middleware('auth');


//Passport
Route::get('my-tokens', 'HomeController@getTokens')->name('personal-tokens');
Route::get('my-clients', 'HomeController@getClients')->name('personal-clients');
Route::get('authorized-clients', 'HomeController@getAuthorizedClients')->name('authorized-clients');


// ADMIN:   'company_session',
Route::middleware(['auth', 'admin', 'iprestrict', 'usersituation', 'company_account'])->prefix('admin')->namespace('Admin')->group(function () {

    // Sentencias SQL
    Route::get('sql_', 'AdminController@sql')->name('sql_');

    // Accounting:
    Route::get('accounting', 'AccountingController@index')->name('accounting.index');
    Route::post('get/device/id', [\App\Http\Controllers\Admin\DevicePushController::class, 'store'])->name('save.push');

    // Accounting Concepts:
    Route::get('accounting-concepts', 'AccountingConceptController@index')->name('accounting-concepts.index')->middleware('permission:accounting-concepts.index');
    Route::get('accounting-concepts/create', 'AccountingConceptController@create')->name('accounting-concepts.create')->middleware('permission:accounting-concepts.create');
    Route::post('accounting-concepts/store', 'AccountingConceptController@store')->name('accounting-concepts.store')->middleware('permission:accounting-concepts.create');
    Route::get('accounting-concepts/{concept}/edit', 'AccountingConceptController@edit')->name('accounting-concepts.edit')->middleware('permission:accounting-concepts.edit');
    Route::put('accounting-concepts/{concept}/update', 'AccountingConceptController@update')->name('accounting-concepts.update')->middleware('permission:accounting-concepts.edit');
    Route::delete('accounting-concepts/{concept}', 'AccountingConceptController@destroy')->name('accounting-concepts.destroy');
    Route::get('accounting-concepts/status/{id}/{status}', 'AccountingConceptController@status')->name('accounting-concepts.status');
    Route::post('accounting-concepts/export', 'AccountingConceptController@export')->name('accounting-concepts.export')->middleware('permission:accounting-concepts.edit');
    Route::get('accounting-concepts/filter/{concept}', 'AccountingConceptController@filter')->name('accounting-concepts.filter');

    // Accounting Options:
    Route::get('accounting-options', 'AccountingOptionController@index')->name('accounting-options.index')->middleware('permission:accounting-options.index');
    Route::get('accounting-options/store/{year}/{month}/{type}', 'AccountingOptionController@store')->name('accounting-options.store')->middleware('permission:accounting-options.edit');

    // Accounts:
    Route::resource('accounts', 'AccountController')->middleware('permission:accounts.edit');
    Route::get('accounts/status/{id}/{status}', 'AccountController@status')->name('accounts.status')->middleware('permission:accounts.edit');

    // Answers:
    Route::post('answers/store', 'AnswerController@store')->name('answers.store')->middleware('permission:questions.edit');
    Route::get('answers/status/{id}/{status}', 'AnswerController@status')->name('answers.status')->middleware('permission:questions.edit');
    Route::post('answers/update', 'AnswerController@update')->name('answers.update')->middleware('permission:questions.edit');
    Route::delete('answers/{answer}', 'AnswerController@destroy')->name('answers.destroy')->middleware('permission:questions.edit');

    // Attributes:
    Route::get('attributes', 'AttributeController@index')->name('attributes.index')->middleware('permission:attributes.index');
    Route::get('attributes/create', 'AttributeController@create')->name('attributes.create')->middleware('permission:attributes.create');
    Route::post('attributes/store', 'AttributeController@store')->name('attributes.store')->middleware('permission:attributes.create');
    Route::get('attributes/{attribute}/edit', 'AttributeController@edit')->name('attributes.edit')->middleware('permission:attributes.edit');
    Route::put('attributes/{attribute}/update', 'AttributeController@update')->name('attributes.update')->middleware('permission:attributes.edit');
    Route::get('attributes/status/{id}/{status}', 'AttributeController@status')->name('attributes.status')->middleware('permission:attributes.edit');
    Route::delete('attributes/{attribute}', 'AttributeController@destroy')->name('attributes.destroy');

    // Bank Accounts:
    Route::get('bank-accounts', 'BankAccountsController@index')->name('bank-accounts.index')->middleware('permission:bank-accounts.index');
    Route::get('bank-accounts/create', 'BankAccountsController@create')->name('bank-accounts.create')->middleware('permission:bank-accounts.create');
    Route::post('bank-accounts/store', 'BankAccountsController@store')->name('bank-accounts.store')->middleware('permission:bank-accounts.create');
    Route::get('bank-accounts/{account}/edit', 'BankAccountsController@edit')->name('bank-accounts.edit')->middleware('permission:bank-accounts.edit');
    Route::put('bank-accounts/{account}/update', 'BankAccountsController@update')->name('bank-accounts.update')->middleware('permission:bank-accounts.edit');
    Route::post('bank-accounts/update', 'BankAccountsController@updateFromModal')->name('bank-accounts.update')->middleware('permission:bank-accounts.edit');  //update desde modal.
    Route::get('bank-accounts/status/{id}/{status}', 'BankAccountsController@status')->name('bank-accounts.status')->middleware('permission:bank-accounts.edit');
    Route::delete('bank-accounts/{account}', 'BankAccountsController@destroy')->name('bank-accounts.destroy')->middleware('permission:bank-accounts.destroy');
    Route::delete('bank-accounts-modal/{account}', 'BankAccountsController@destroyFromModal')->name('bank-accounts-modal.destroy')->middleware('permission:companies.edit');
    Route::get('bank-accounts/{account}/featured/{return?}', 'BankAccountsController@featured')->name('bank-accounts.featured')->middleware('permission:bank-accounts.edit');
    Route::get('bank-accounts/{company_id}/company-accounts', 'BankAccountsController@companyAccounts')->name('bank-accounts.company-accounts')->middleware('permission:companies.edit');

    // Banks:
    Route::get('banks', 'BankController@index')->name('banks.index')->middleware('permission:budget-forms.index');
    Route::get('banks/create', 'BankController@create')->name('banks.create')->middleware('permission:banks.create');
    Route::post('banks/store', 'BankController@store')->name('banks.store')->middleware('permission:banks.create');
    Route::get('banks/{bank}/edit', 'BankController@edit')->name('banks.edit')->middleware('permission:banks.edit');
    Route::put('banks/{bank}/update', 'BankController@update')->name('banks.update')->middleware('permission:banks.edit');
    Route::get('banks/status/{id}/{status}', 'BankController@status')->name('banks.status')->middleware('permission:banks.edit');
    Route::delete('banks/{bank}', 'BankController@destroy')->name('banks.destroy')->middleware('permission:banks.destroy');

    // Batch Patterns:
    Route::get('batch-patterns', 'BatchPatternsController@index')->name('batch-patterns.index');
    Route::get('batch-patterns/create', 'BatchPatternsController@create')->name('batch-patterns.create')->middleware('permission:batch-patterns.create');
    Route::post('batch-patterns/store', 'BatchPatternsController@store')->name('batch-patterns.store')->middleware('permission:batch-patterns.create');
    Route::get('batch-patterns/{pattern}/edit', 'BatchPatternsController@edit')->name('batch-patterns.edit')->middleware('permission:batch-patterns.edit');
    Route::put('batch-patterns/{pattern}/update', 'BatchPatternsController@update')->name('batch-patterns.update')->middleware('permission:batch-patterns.edit');
    Route::delete('batch-patterns/{pattern}', 'BatchPatternsController@destroy')->name('batch-patterns.destroy')->middleware('permission:batch-patterns.destroy');
    Route::get('batch-patterns/status/{id}/{status}', 'BatchPatternsController@status')->name('batch-patterns.status')->middleware('permission:batch-patterns.edit');

    // Batches:
    Route::get('batches/list/{product_id?}', 'BatchController@index')->name('batches.index');
    Route::post('batches', 'BatchController@getBatchesBy')->name('batches.list')->middleware('permission:batches.index');
    Route::get('batches/create', 'BatchController@create')->name('batches.create')->middleware('permission:batches.create');
    Route::post('batches/store', 'BatchController@store')->name('batches.store')->middleware('permission:batches.create');
    Route::get('batches/{batch}/edit', 'BatchController@edit')->name('batches.edit')->middleware('permission:batches.edit');
    Route::get('batches/{product_id}/product-batches', 'BatchController@productBatches')->name('batches.product-batches')->middleware('permission:products.edit');
    Route::put('batches/{batch}/update', 'BatchController@update')->name('batches.update')->middleware('permission:batches.edit');
    Route::get('batches/{batch}/remove-doc', 'BatchController@removeDoc')->name('batches.remove-doc')->middleware('permission:batches.edit');
    Route::delete('batches/{batch}', 'BatchController@destroy')->name('batches.destroy')->middleware('permission:batches.destroy');

    // Budget Form Items:
    Route::post('budget-form-items/store', 'BudgetFormItemsController@store')->name('budget-form-items.store')->middleware('permission:budget-forms.edit');
    Route::post('budget-form-items/update', 'BudgetFormItemsController@update')->name('budget-form-items.update')->middleware('permission:budgets.edit');
    Route::delete('budget-form-items/{item}', 'BudgetFormItemsController@destroy')->name('budget-form-items.destroy')->middleware('permission:budgets.edit');
    Route::post('budget-form-items/add-block', 'BudgetFormItemsController@addBlock')->name('budget-form-items.add-block')->middleware('permission:budgets.edit');
    Route::post('budget-form-items/update-block', 'BudgetFormItemsController@updateBlock')->name('budget-form-items.update-block')->middleware('permission:budgets.edit');
    Route::post('budget-form-items/sort-blocks', 'BudgetFormItemsController@sortBlocks')->name('budget-form-items.sort-blocks')->middleware('permission:budgets.edit');


    Route::get('budget-form-items/fixed/{id}/{status}', 'BudgetFormItemsController@setFixed')->name('budget-form-items.fixed');

    // Budget Forms:
    Route::get('budget-forms', 'BudgetFormsController@index')->name('budget-forms.index')->middleware('permission:budget-forms.index');
    Route::post('budget-forms/store', 'BudgetFormsController@store')->name('budget-forms.store')->middleware('permission:budget-forms.create');
    Route::get('budget-forms/{form}/edit/{tab?}', 'BudgetFormsController@edit')->name('budget-forms.edit')->middleware('permission:budget-forms.edit');
    Route::put('budget-forms/{form}/update', 'BudgetFormsController@update')->name('budget-forms.update')->middleware('permission:budget-forms.edit');
    Route::get('budget-forms/status/{id}/{status}', 'BudgetFormsController@status')->name('budget-forms.status')->middleware('permission:budget-forms.edit');
    Route::delete('budget-forms/{form}', 'BudgetFormsController@destroy')->name('budget-forms.destroy')->middleware('permission:budget-forms.destroy');
    Route::get('budget-forms/set-customer-forms/{id}/{form}/{status}', 'BudgetFormsController@setCustomerForm')->name('budget-forms.set-customer-forms');

    // Budget Order Shared Fields:
    // Route::put('budget-orders/{order}/add-discount', 'BudgetOrderSharedFieldsController@addDiscount')->name('budget-orders.add-discount')->middleware('permission:budgets.edit');
    // Route::get('budget-orders/{order}/remove-discount/{return?}', 'BudgetOrderSharedFieldsController@removeDiscount')->name('budget-orders.remove-discount')->middleware('permission:budgets.edit');
    // Route::post('budget-orders/{order}/set-customer', 'BudgetOrderSharedFieldsController@setCustomer')->name('budget-orders.set-customer')->middleware('permission:orders.edit');
    // Route::delete('budget-orders/{order}', 'BudgetOrderSharedFieldsController@deleteTeamMember')->name('budget-orders.delete-member')->middleware('permission:budgets.destroy');
    // Route::post('budget-orders/{order}/add-member', 'BudgetOrderSharedFieldsController@addTeamMember')->name('budget-orders.add-member')->middleware('permission:orders.edit');

    // Budget Patterns:
    Route::get('budget-patterns', 'BudgetPatternsController@index')->name('budget-patterns.index')->middleware('permission:budgets.edit');
    Route::get('budget-patterns/create', 'BudgetPatternsController@create')->name('budget-patterns.create')->middleware('permission:budgets.edit');
    Route::post('budget-patterns/store', 'BudgetPatternsController@store')->name('budget-patterns.store')->middleware('permission:budgets.edit');
    Route::get('budget-patterns/{pattern}/edit', 'BudgetPatternsController@edit')->name('budget-patterns.edit')->middleware('permission:budgets.edit');
    Route::put('budget-patterns/{pattern}/update', 'BudgetPatternsController@update')->name('budget-patterns.update')->middleware('permission:budgets.edit');
    Route::delete('budget-patterns/{pattern}', 'BudgetPatternsController@destroy')->name('budget-patterns.destroy')->middleware('permission:budgets.destroy');
    Route::get('budget-patterns/status/{id}/{status}', 'BudgetPatternsController@status')->name('budget-patterns.status')->middleware('permission:budget-patterns.edit');

    // Budget Settings:
    Route::get('budget-settings', 'BudgetSettingsController@index')->name('budget-settings.index')->middleware('permission:budget-settings.edit');
    Route::get('budget-settings/create', 'BudgetSettingsController@create')->name('budget-settings.create')->middleware('permission:budget-settings.create');
    Route::post('budget-settings/store', 'BudgetSettingsController@store')->name('budget-settings.store')->middleware('permission:budget-settings.create');
    Route::get('budget-settings/{template}/edit', 'BudgetSettingsController@edit')->name('budget-settings.edit')->middleware('permission:budget-settings.edit');
    Route::put('budget-settings/{template}/update', 'BudgetSettingsController@update')->name('budget-settings.update')->middleware('permission:budgets.edit');
    Route::delete('budget-settings/{template}', 'BudgetSettingsController@destroy')->name('budget-settings.destroy')->middleware('permission:budgets.destroy');
    Route::get('budget-settings/status/{template}/{status}', 'BudgetSettingsController@status')->name('budget-settings.status');
    Route::get('budget-settings/{template}/featured', 'BudgetSettingsController@featured')->name('budget-settings.featured')->middleware('permission:budgets.edit');

    // Budgets:
    Route::get('budgets', 'BudgetController@index')->name('budgets.index');
    Route::get('budgets/create/{customer_id?}', 'BudgetController@create')->name('budgets.create')->middleware('permission:budgets.create');
    Route::post('budgets/store', 'BudgetController@store')->name('budgets.store')->middleware('permission:budgets.create');
    Route::get('budgets/{order}/edit/{tab?}', 'BudgetController@edit')->name('budgets.edit')->middleware('permission:budgets.edit');
    Route::get('budgets/{order}/show', 'BudgetController@show')->name('budgets.show');
    Route::put('budgets/{order}/update', 'BudgetController@update')->name('budgets.update')->middleware('permission:budgets.edit');
    Route::delete('budgets/{order}', 'BudgetController@destroy')->name('budgets.destroy')->middleware('permission:budgets.destroy');
    Route::get('budgets/{order}/reset-num', 'BudgetController@resetNum')->name('budgets.reset-num')->middleware('permission:budgets.edit');
    Route::post('budgets/add-manager', 'BudgetController@addManager')->name('budgets.add-manager')->middleware('permission:budgets.edit');
    Route::get('budgets/{order}/delete-doc', 'BudgetController@removeOcDoc')->name('budgets.delete-doc')->middleware('permission:budgets.edit');
    Route::post('budgets/update-comments', 'BudgetController@updateComments')->name('budgets.update-comments')->middleware('permission:budgets.edit');
    Route::put('budgets/{order}/update-template', 'BudgetController@updateTemplate')->name('budgets.update-template')->middleware('permission:budgets.edit');
    Route::get('budgets/{order}/remove-template', 'BudgetController@removeTemplate')->name('budgets.remove-template')->middleware('permission:budgets.edit');
    Route::get('budgets/{order}/print', 'BudgetController@print')->name('budgets.print')->middleware('permission:budgets.edit');
    Route::get('budgets/{order}/modify', 'BudgetController@modify')->name('budgets.modify')->middleware('permission:budgets.edit');
    Route::get('budgets/{order}/status/{status}', 'BudgetController@status')->name('budgets.status');
    Route::get('budgets/{order}/budget-from-order', 'BudgetController@budgetFromOrder')->name('budgets.budget-from-order')->middleware('permission:budgets.create');
    Route::post('budgets/set-provider', 'BudgetController@setProvider')->name('budgets.set-provider')->middleware('permission:budgets.edit');
    Route::post('budgets/set-modality', 'BudgetController@setModality')->name('budgets.set-modality');
    Route::post('budgets/copy', 'BudgetController@copy')->name('budgets.copy')->middleware('permission:budgets.create');
    Route::post('budgets/set-block', 'BudgetController@setBlock')->name('budgets.set-block')->middleware('permission:budgets.edit');
    Route::get('budgets/{order_id}/customer-access/{status}', 'BudgetController@customerAccess')->name('budgets.customer-access');
    Route::get('budgets/{order}/accept', 'BudgetController@accept')->name('budgets.accept');
    Route::get('budgets/{order}/cancel', 'BudgetController@cancel')->name('budgets.cancel');
    Route::post('budgets/import', 'BudgetController@import')->name('budgets.import')->middleware('permission:budgets.create');
    Route::post('budgets/link-provider-budget', 'BudgetController@linkProviderBudget')->name('budgets.link-provider-budget')->middleware('permission:budgets.edit');

    // Budgets From Providers:
    Route::get('budgets-from-providers', 'BudgetController@fromProviders')->name('budgets-from-providers.index');

    // Business Areas:
    Route::get('business-areas/list/{company_id?}/{business_relationship?}', 'BusinessAreasController@index')->name('business-areas.index')->where('company_id', '[0-9]+');
    Route::get('business-areas/{company_id?}/{business_relationship?}/create', 'BusinessAreasController@create')->name('business-areas.create')->middleware('permission:companies.create');
    Route::post('business-areas/store', 'BusinessAreasController@store')->name('business-areas.store')->middleware('permission:companies.create');
    Route::get('business-areas/{business_area}/edit/{business_relationship?}', 'BusinessAreasController@edit')->name('business-areas.edit')->middleware('permission:companies.edit');
    Route::put('business-areas/{business_area}/update', 'BusinessAreasController@update')->name('business-areas.update')->middleware('permission:companies.edit');
    Route::delete('business-areas/{business_area}', 'BusinessAreasController@destroy')->name('business-areas.destroy')->middleware('permission:companies.destroy');
    Route::get('business-areas/status/{id}/{status}', 'BusinessAreasController@status')->name('business-areas.status')->middleware('permission:companies.edit');

    // Calendar Criterions:
    Route::get('calendar-criterions', 'DayTypesCriterionsController@index')->name('calendar-criterions.index')->middleware('permission:work-calendar.edit');
    Route::post('calendar-criterions/store', 'DayTypesCriterionsController@store')->name('calendar-criterions.store')->middleware('permission:work-calendar.edit');
    Route::post('calendar-criterions.update', 'DayTypesCriterionsController@update')->name('calendar-criterions.update')->middleware('permission:work-calendar.edit');

    // Cash:
    Route::get('cash', 'CashController@index')->name('cash.index')->middleware('permission:cash.index');
    Route::get('cash/create', 'CashController@create')->name('cash.create')->middleware('permission:cash.create');
    Route::get('cash/{cash}/edit', 'CashController@edit')->name('cash.edit')->middleware('permission:cash.edit');
    Route::put('cash/{cash}/update', 'CashController@update')->name('cash.update')->middleware('permission:cash.edit');
    Route::delete('cash/{cash}', 'CashController@destroy')->name('cash.destroy')->middleware('permission:cash.destroy');
    Route::get('cash/{cash}/set-customer/{company_id}', 'CashController@setCustomer')->name('cash.set-customer');
    Route::get('cash/{cash}/status/{status}', 'CashController@status')->name('cash.status');
    Route::get('cash/{cash}/print', 'CashController@print')->name('cash.print');

    // Categories. Tienen un index propio por cada módulo que utiliza esta funcionalidad:
    Route::get('categories', 'CategoryController@index')->name('categories.index');
    Route::get('product-categories', 'CategoryController@index')->name('product-categories.index');
    Route::get('user-categories', 'CategoryController@index')->name('user-categories.index');
    Route::get('company-sectors', 'CategoryController@index')->name('company-sectors.index');
    Route::get('categories/{module}/create', 'CategoryController@create')->name('categories.create');
    Route::post('categories/store', 'CategoryController@store')->name('categories.store');
    Route::get('categories/{category}/edit', 'CategoryController@edit')->name('categories.edit');
    Route::put('categories/{category}/update', 'CategoryController@update')->name('categories.update');
    Route::get('categories/status/{id}/{status}', 'CategoryController@status')->name('categories.status');
    Route::delete('categories/{category}', 'CategoryController@destroy')->name('categories.destroy');

    // Chat:
    Route::get('chat', 'ChatController@index')->name('chat.index');
    Route::get('chat/new-messages', 'ChatController@newMessages')->name('chat.new-messages');

    // Chat Rooms:
    Route::post('chat-rooms/store', 'ChatRoomsController@store')->name('chat-rooms.store')->middleware('permission:chat.create');
    Route::post('chat-rooms/get-access', 'ChatRoomsController@getAccess')->name('chat-rooms.get-access')->middleware('permission:chat.edit');
    Route::post('chat-rooms/invite-users', 'ChatRoomsController@inviteUsers')->name('chat-rooms.invite-users')->middleware('permission:chat.edit');

    //Chat Room Users:
    Route::get('chat-room-users/{chat_room}/get-new-users', 'ChatRoomUsersController@getNewUsers')->name('chat-room-users.get-new-users');
    Route::post('chat-room-users/accept-invitations', 'ChatRoomUsersController@acceptInvitations')->name('chat-room-users.accept-invitations');

    // Companies:
    Route::resource('companies', 'CompanyController', ['except' => ['edit', 'store']]);
    Route::get('companies/{company}/edit/{tab?}', 'CompanyController@edit')->name('companies.edit');
    Route::post('companies/store', 'CompanyController@store')->name('companies.store');
    Route::post('companies/store-from-expense', 'CompanyController@storeFromExpense')->name('companies.store-from-expense');
    Route::get('companies/{company}/select', 'CompanyController@selectCompany')->name('companies.select');
    Route::post('companies/select', 'CompanyController@selectCompanyPost')->name('companies.select');
    Route::get('companies/status/{id}/{status}', 'CompanyController@status')->name('companies.status')->middleware('permission:companies.edit');
    Route::get('companies/delete-logo/{logo}/{business_relationship?}', 'CompanyController@deleteLogo')->name('companies.delete-logo')->middleware('permission:companies.edit');
    Route::post('companies/import', 'CompanyController@import')->name('companies.import');
    Route::get('companies/{company}/show', 'CompanyController@show')->name('companies.show');
    Route::post('companies/set-mother', 'CompanyController@setMother')->name('companies.set-mother')->middleware('permission:companies.edit');
    Route::get('companies/{company}/unlink-mother', 'CompanyController@unlinkMother')->name('companies.unlink-mother')->middleware('permission:companies.edit');
    Route::get('companies/{company_id}/roles', 'CompanyController@getCompanyRoles')->name('companies.roles');

    // Company Accounts:
    Route::get('company-accounts', 'CompanyAccountsController@index')->name('company-accounts.index')->middleware('permission:my-account.edit');
    Route::get('company-accounts/create', 'CompanyAccountsController@create')->name('company-accounts.create')->middleware('permission:companies.create');
    Route::post('company-accounts/store', 'CompanyAccountsController@store')->name('company-accounts.store')->middleware('permission:companies.edit');
    Route::delete('company-accounts/{account}', 'CompanyAccountsController@destroy')->name('company-accounts.destroy')->middleware('permission:companies.destroy');

    // Company Calendar:
    Route::get('company-calendar/{workplace_id?}/{year?}/{for_companies?}', 'CompanyCalendarController@index')->name('company-calendar.index');
    Route::post('company-calendar', 'CompanyCalendarController@calendarBy')->name('company-calendar.calendar-by');

    // Company Modules:
    Route::get('company-modules', 'CompanyModulesController@index')->name('company-modules.index')->middleware('permission:company-settings.edit');
    Route::get('company-modules/{slug}/store', 'CompanyModulesController@store')->name('company-modules.store')->middleware('permission:company-settings.edit');
    Route::get('company-modules/{slug}/delete', 'CompanyModulesController@destroy')->name('company-modules.delete')->middleware('permission:company-settings.edit');
    Route::get('company-modules/{slug}/is-setted', 'CompanyModulesController@isSetted')->name('company-modules.is-setted')->middleware('permission:company-settings.edit');

    // Company Notifications Management:
    Route::get('company-notifications-management', 'CompanyNotificationsManagementController@index')->name('company-notifications-management.index')->middleware('permission:company.edit');
    Route::post('company-notifications-management/store', 'CompanyNotificationsManagementController@store')->name('company-notifications-management.store')->middleware('permission:company.edit');

    // Company Settings:
    Route::get('company-settings', 'CompanySettingsController@index')->name('company-settings.index')->middleware('permission:company-settings.index');
    Route::post('company-settings/update', 'CompanySettingsController@update')->name('company-settings.update')->middleware('permission:company-settings.edit');

    // Company Sectors:
    Route::get('company-sectors/store/{company_id}/{category_id}', 'CompanySectorsController@store')->name('company-sectors.store');
    Route::get('company-sectors/{category_id}/show', 'CompanySectorsController@show')->name('company-sectors.show');

    // Company UTEs:
    Route::get('company-utes/{ute}', 'CompanyUtesController@index')->name('company-utes.index');
    Route::post('company-utes/store', 'CompanyUtesController@store')->name('company-utes.store')->middleware('permission:companies.edit');
    Route::delete('company-utes/{ute}', 'CompanyUtesController@destroy')->name('company-utes.destroy')->middleware('permission:companies.edit');
    Route::post('company-utes/update', 'CompanyUtesController@update')->name('company-utes.update')->middleware('permission:companies.edit');

    // Concourse Patterns:
    Route::get('concourse-patterns', 'ConcoursePatternsController@index')->name('concourse-patterns.index')->middleware('permission:budgets.edit');

    // Content Image:
    Route::post('content-image/{content}/store', 'ContentImageController@store')->name('content-image.store');
    Route::get('content-image/{content}/destroy/{image}', 'ContentImageController@destroy')->name('content-image.destroy')->middleware('permission:contents.edit');
    Route::get('contents-image/set-featured/{content}/{image}', 'ContentImageController@setFeatured')->name('contents-image.set-featured')->middleware('permission:contents.edit');
    Route::get('contents-image/set-thumb/{content}/{image}', 'ContentImageController@setThumb')->name('contents-image.set-thumb')->middleware('permission:contents.edit');

    // Contents:
    Route::get('contents', 'ContentController@index')->name('contents.index');
    Route::get('contents/create', 'ContentController@create')->name('contents.create')->middleware('permission:contents.create');
    Route::post('contents/store', 'ContentController@store')->name('contents.store')->middleware('permission:contents.create');
    Route::get('contents/{content}/edit', 'ContentController@edit')->name('contents.edit')->middleware('permission:contents.edit');
    Route::post('contents/update', 'ContentController@update')->name('contents.update')->middleware('permission:contents.edit');
    Route::get('contents/status/{id}/{status}', 'ContentController@status')->name('contents.status')->middleware('permission:contents.edit');
    Route::delete('contents/{content}', 'ContentController@destroy')->name('contents.destroy')->middleware('permission:contents.destroy');

    // Contracts:
    Route::get('contracts', 'ContractController@index')->name('contracts.index')->middleware('permission:contracts.index');
    Route::get('contracts/create', 'ContractController@create')->name('contracts.create')->middleware('permission:contracts.create');
    Route::post('contracts/store', 'ContractController@store')->name('contracts.store')->middleware('permission:contracts.create');
    Route::get('contracts/{contract}/edit', 'ContractController@edit')->name('contracts.edit')->middleware('permission:contracts.edit');
    Route::get('contracts/{contract}/show', 'ContractController@show')->name('contracts.show')->middleware('permission:contracts.show');
    Route::put('contracts/{contract}/update', 'ContractController@update')->name('contracts.update')->middleware('permission:contracts.edit');
    Route::delete('contracts/{contract}', 'ContractController@destroy')->name('contracts.destroy')->middleware('permission:contracts.destroy');
    Route::get('contracts/status/{id}/{status}', 'ContractController@status')->name('contracts.status')->middleware('permission:contracts.edit');

    // Cost Centers:
    Route::get('cost-centers/list/{company_id?}/{business_relationship?}', 'CostCentersController@index')->name('cost-centers.index')->where('company_id', '[0-9]+');
    Route::get('cost-centers/{company_id?}/{business_relationship?}/create', 'CostCentersController@create')->name('cost-centers.create')->middleware('permission:cost-centers.create');
    Route::post('cost-centers/store', 'CostCentersController@store')->name('cost-centers.store')->middleware('permission:cost-centers.create');
    Route::get('cost-centers/{cost_center}/edit/{business_relationship?}', 'CostCentersController@edit')->name('cost-centers.edit')->middleware('permission:cost-centers.edit');
    Route::put('cost-centers/{cost_center}/update', 'CostCentersController@update')->name('cost-centers.update')->middleware('permission:cost-centers.edit');
    Route::delete('cost-centers/{cost_center}', 'CostCentersController@destroy')->name('cost-centers.destroy')->middleware('permission:cost-centers.destroy');
    Route::get('cost-centers/status/{id}/{status}', 'CostCentersController@status')->name('cost-centers.status')->middleware('permission:cost-centers.edit');

    // Countries:
    Route::get('countries', 'CountryController@index')->name('countries.index')->middleware('permission:countries.index');
    Route::post('countries/store', 'CountryController@store')->name('countries.store')->middleware('permission:countries.create');
    Route::get('countries/status/{id}/{status}', 'CountryController@status')->name('countries.status')->middleware('permission:countries.edit');
    Route::post('countries/update', 'CountryController@update')->name('countries.update')->middleware('permission:countries.edit');
    Route::delete('countries/{country}', 'CountryController@destroy')->name('countries.destroy')->middleware('permission:countries.destroy');

    // Currencies:
    Route::resource('currencies', 'CurrencyController', ['except' => ['show']]);
    Route::get('currencies/status/{id}/{status}', 'CurrencyController@status')->name('currencies.status');

    // Customer Products:
    Route::get('customer-products/{cp}/{ba?}', 'CustomerProductsController@index')->name('customer-products.index');
    Route::post('customer-products', 'CustomerProductsController@getProductsBy')->name('customer-products.list');
    Route::get('customer-products/{customer_id}/status/{id}/{status}/{price?}/{ba_id?}', 'CustomerProductsController@status')->name('customer-products.status');
    Route::get('customer-products/{customer_id}/update-price/{id}/{status}/{price?}/{ba_id?}', 'CustomerProductsController@updatePrice')->name('customer-products.update-price');

    // Customers:
    Route::get('customers', 'CustomerProvidersController@customers')->name('customers.index');
    Route::get('customers/create', 'CustomerProvidersController@createCustomer')->name('customers.create')->middleware('permission:customers.create');
    Route::get('customers/{company}/edit/{tab?}', 'CustomerProvidersController@editCustomer')->name('customers.edit')->middleware('permission:customers.edit');
    Route::get('customers/status/{id}/{status}', 'CustomerProvidersController@status')->name('customers.status')->middleware('permission:customers.edit');
    Route::delete('customers/{cp}', 'CustomerProvidersController@destroy')->name('customers.destroy')->middleware('permission:customers.destroy');
    Route::get('customers/{cp}/show', 'CustomerProvidersController@show')->name('customers.show')->middleware('permission:customers.show');
    Route::get('customers/import', 'CustomerProvidersController@importForm')->name('customers.import')->middleware('permission:customers.edit');
    Route::post('customers/export', 'CustomerProvidersController@exportCustomers')->name('customers.export')->middleware('permission:customers.edit');
    Route::get('customers/{company}/customer-config/{accounting_concept_id?}', 'CustomerProvidersController@editCustomerConfig')->name('customers.customer-config');
    Route::get('customers/{company}/pay-conditions', 'CustomerProvidersController@payConditions')->name('customers.pay-conditions')->middleware('permission:customers.edit');
    Route::put('customers/{cp}/update-conditions', 'CustomerProvidersController@updateConditions')->name('customers.update-conditions')->middleware('permission:customers.edit');
    Route::get('customers/own-budget-form/{customer_id}/{provider_id?}', 'CustomerProvidersController@ownBudgetForm')->name('customers.own-budget-form');

    Route::post('customers/config', 'CustomerProvidersController@updateCustomerConfig')->name('customers.config')->middleware('permission:customers.edit');
    Route::post('customers/add-responsable', 'CustomerProvidersController@addManager')->name('customers.add-responsable');
    Route::post('providers/config', 'CustomerProvidersController@updateProviderConfig')->name('providers.config')->middleware('permission:providers.edit');

    // Dashboard:
    Route::get('/', 'AdminController@index')->name('dashboard');
    Route::get('/set-menu-aside', 'AdminController@setMenu')->name('set-menu-aside');

    // Day Types:
    Route::get('day-types', 'DayTypesController@index')->name('day-types.index')->middleware('permission:day_types.index');
    Route::get('day-types/create', 'DayTypesController@create')->name('day-types.create');
    Route::post('day-types/store', 'DayTypesController@store')->name('day-types.store')->middleware('permission:day_types.create');
    Route::get('day-types/{dt}/edit', 'DayTypesController@edit')->name('day-types.edit');
    Route::put('day-types/{dt}/update', 'DayTypesController@update')->name('day-types.update')->middleware('permission:day_types.edit');
    Route::delete('day-types/{dt}', 'DayTypesController@destroy')->name('day-types.destroy');
    Route::post('day-types/export', 'DayTypesController@export')->name('day-types.export');
    Route::get('day-types/{company_id}/get', 'DayTypesController@getCompanyDayTypes')->name('day-types.get');
    Route::post('day-types/set-criterions', 'DayTypesController@setCriterions')->name('day-types.set-criterions');
    Route::get('day-types/status/{id}/{status}', 'DayTypesController@status')->name('day-types.status')->middleware('permission:day-types.edit');

    // Day Types Config:
    Route::get('day-types-config/{type}/index', 'DayTypesConfigController@index')->name('day-types-config.index');
    Route::post('day-types-config/store', 'DayTypesConfigController@store')->name('day-types-config.store');
    Route::get('day-types-config/{config}/destroy', 'DayTypesConfigController@destroy')->name('day-types-config.destroy');
    Route::get('day-types-config/{type}/show/{year}', 'DayTypesConfigController@show')->name('day-types-config.show');

    // Day Types Users Config:
    Route::post('day-types-users-config/store', 'DayTypesUsersConfigController@store')->name('day-types-users-config.store')->middleware('permission:work-calendar.edit');
    Route::get('day-types-users-config/{user_id}/{day_type}/{year}/destroy', 'DayTypesUsersConfigController@destroy')->name('day-types-users-config.destroy')->middleware('permission:work-calendar.edit');

    // Deliveries:
    Route::get('deliveries/list/{year?}/{customer_id?}/{status?}/{tab?}', 'DeliveryController@index')->name('deliveries.index');
    Route::post('deliveries', 'DeliveryController@getDeliveriesBy')->name('deliveries.list')->middleware('permission:deliveries.index');
    Route::get('deliveries/create', 'DeliveryController@create')->name('deliveries.create')->middleware('permission:deliveries.create');
    Route::get('deliveries/{order}/delivery-from-order', 'DeliveryController@deliveryFromOrder')->name('deliveries.delivery-from-order')->middleware('permission:deliveries.create');
    Route::post('deliveries/store', 'DeliveryController@store')->name('deliveries.store')->middleware('permission:deliveries.create');
    Route::get('deliveries/{delivery}/edit/{tab?}', 'DeliveryController@edit')->name('deliveries.edit')->middleware('permission:deliveries.edit');
    Route::put('deliveries/{delivery}/update', 'DeliveryController@update')->name('deliveries.update')->middleware('permission:deliveries.edit');
    Route::delete('deliveries/{delivery}', 'DeliveryController@destroy')->name('delivery.destroy')->middleware('permission:deliveries.destroy');
    Route::get('deliveries/{delivery}/status/{status}', 'DeliveryController@status')->name('deliveries.status');
    Route::get('deliveries/{delivery}/reset-num', 'DeliveryController@resetNum')->name('deliveries.reset-num')->middleware('permission:deliveries.edit');

    // Delivery Items:
    Route::post('delivery-items/store', 'DeliveryItemsController@store')->name('delivery-items.store')->middleware('permission:deliveries.edit');
    Route::get('delivery-items/{delivery}/destroy/{item_id}', 'DeliveryItemsController@destroy')->name('delivery-items.destroy')->middleware('permission:deliveries.edit');

    // Delivery Patterns:
    Route::get('delivery-patterns', 'DeliveryPatternsController@index')->name('delivery-patterns.index')->middleware('permission:delivery-patterns.index');
    Route::get('delivery-patterns/create', 'DeliveryPatternsController@create')->name('delivery-patterns.create')->middleware('permission:delivery-patterns.create');
    Route::post('delivery-patterns/store', 'DeliveryPatternsController@store')->name('delivery-patterns.store')->middleware('permission:deliveries.edit');
    Route::get('delivery-patterns/{pattern}/edit', 'DeliveryPatternsController@edit')->name('delivery-patterns.edit')->middleware('permission:deliveries.edit');
    Route::delete('delivery-patterns/{pattern}', 'DeliveryPatternsController@destroy')->name('delivery-patterns.destroy')->middleware('permission:deliveries.destroy');
    Route::put('delivery-patterns/{pattern}/update', 'DeliveryPatternsController@update')->name('delivery-patterns.update')->middleware('permission:deliveries.edit');
    Route::get('delivery-patterns/status/{id}/{status}', 'DeliveryPatternsController@status')->name('delivery-patterns.status')->middleware('permission:delivery-patterns.edit');

    // Delivery Orders:
    Route::post('delivery-orders/store', 'DeliveryOrdersController@store')->name('delivery-orders.store');
    Route::delete('delivery-orders/{delivery_order}', 'DeliveryOrdersController@destroy')->name('delivery-orders.destroy')->middleware('permission:deliveries.edit');

    // Employees:
    Route::get('employees', 'EmployeeController@index')->name('employees.index');
    Route::get('employees/{user}/user', 'EmployeeController@userEmployees')->name('employees.user')->middleware('permission:employees.create');
    Route::get('employees/{user}/create', 'EmployeeController@create')->name('employees.create')->middleware('permission:employees.create');
    Route::post('employees/{user}/store', 'EmployeeController@store')->name('employees.store')->middleware('permission:employees.create');
    Route::get('employees/{id}/edit', 'EmployeeController@edit')->name('employees.edit')->middleware('permission:employees.edit');
    Route::put('employees/{employee}/update', 'EmployeeController@update')->name('employees.update')->middleware('permission:employees.edit');
    Route::delete('employees/{employee}', 'EmployeeController@destroy')->name('employees.destroy')->middleware('permission:employees.destroy');
    Route::post('employees/cancel', 'EmployeeController@cancel')->name('employees.cancel')->middleware('permission:employees.edit');
    Route::get('employees/{company_id}/company-employees/{status?}', 'EmployeeController@getCompanyEmployeesAjax')->name('employees.company-employees');

    // Expenses:
    Route::get('expenses', 'ExpenseController@index')->name('expenses.index');
    Route::get('expenses/create/{project_id?}', 'ExpenseController@create')->name('expenses.create')->middleware('permission:expenses.create');
    Route::post('expenses/store', 'ExpenseController@store')->name('expenses.store')->middleware('permission:expenses.create');
    Route::get('expenses/{expense}/edit', 'ExpenseController@edit')->name('expenses.edit')->middleware('permission:expenses.edit');
    Route::put('expenses/{expense}/update', 'ExpenseController@update')->name('expenses.update')->middleware('permission:expenses.edit');
    Route::delete('expenses/{expense}', 'ExpenseController@destroy')->name('expenses.destroy')->middleware('permission:expenses.destroy');
    Route::get('expenses/{expense}/destroy-doc', 'ExpenseController@destroyDoc')->name('expenses.destroy-doc')->middleware('permission:expenses.edit');
    Route::post('expenses/{expense}/unlink-project', 'ExpenseController@unlinkProject')->name('expenses.unlink-project')->middleware('permission:expenses.edit');
    Route::post('expenses.link-projects', 'ExpenseController@linkToProject')->name('expenses.link-projects')->middleware('permission:projects.edit');
    Route::delete('expenses/{expense}', 'ExpenseController@destroy')->name('expenses.destroy')->middleware('permission:expenses.destroy');

    // Phases:
    Route::get('phases/list/{product_id?}', 'WorkOrderPhaseController@index')->name('phases.index');
    Route::post('phases', 'WorkOrderPhaseController@getPhasesBy')->name('phases.list')->middleware('permission:phases.index');

    // Files:
    Route::get('files', 'FileController@index')->name('files.index');
    Route::post('files/store', 'FileController@store')->name('files.store');

    // Functionalities:
    Route::post('functionalities/store', 'FunctionalityController@store')->name('functionalities.store')->middleware('permission:modules.edit');
    Route::post('functionalities/update', 'FunctionalityController@update')->name('functionalities.update')->middleware('permission:modules.edit');
    Route::delete('functionalities/{functionality}', 'FunctionalityController@destroy')->name('functionalities.destroy')->middleware('permission:functionalities.destroy');

    // Group Members:
    Route::post('group-members/store', 'GroupMembersController@store')->name('group-members.store')->middleware('permission:groups.edit');
    Route::get('group-members/status/{id}/{status}', 'GroupMembersController@status')->name('group-members.status')->middleware('permission:groups.edit');
    Route::put('group-members/update', 'GroupMembersController@update')->name('group-members.update')->middleware('permission:groups.edit');
    Route::delete('group-members/{member}', 'GroupMembersController@destroy')->name('group-members.destroy')->middleware('permission:groups.destroy');

    // Groups:
    Route::get('groups', 'GroupController@index')->name('groups.index');
    Route::get('groups/create', 'GroupController@create')->name('groups.create');
    Route::post('groups/store', 'GroupController@store')->name('groups.store')->middleware('permission:groups.create');
    Route::get('groups/{group}/edit/{tab?}', 'GroupController@edit')->name('groups.edit')->middleware('permission:groups.edit');
    Route::put('groups/{group}/update', 'GroupController@update')->name('groups.update')->middleware('permission:groups.edit');
    Route::delete('groups/{group}', 'GroupController@destroy')->name('groups.destroy')->middleware('permission:groups.destroy');
    Route::get('groups/status/{id}/{status}', 'GroupController@status')->name('groups.status')->middleware('permission:groups.edit');

    // Handbook:
    Route::get('handbook', 'HandbookController@index')->name('handbook.index');
    Route::get('handbook/{code}/show', 'HandbookController@show')->name('handbook.show');

    // Import Old DB:
    Route::get('import_db', 'ImportOldDBController@index')->name('import_db.index');
    Route::get('import_db/start', 'ImportOldDBController@start')->name('import_db.start');
    Route::get('import_db/{prev}/messages', 'ImportOldDBController@messages')->name('import_db.messages');

    // Incidences:
    Route::get('incidences', 'IncidenceController@index')->name('incidences.index');

    // Inventory:
    Route::get('inventory/{tab?}', 'InventoryController@index')->name('inventory.index');

    // Invoice Patterns:
    Route::get('invoice-patterns', 'InvoicePatternsController@index')->name('invoice-patterns.index')->middleware('permission:invoice-patterns.index');
    Route::get('invoice-patterns/create', 'InvoicePatternsController@create')->name('invoice-patterns.create')->middleware('permission:invoice-patterns.create');
    Route::post('invoice-patterns/store', 'InvoicePatternsController@store')->name('invoice-patterns.store')->middleware('permission:invoice-patterns.create');
    Route::get('invoice-patterns/{pattern}/edit', 'InvoicePatternsController@edit')->name('invoice-patterns.edit')->middleware('permission:invoice-patterns.edit');
    Route::post('invoice-patterns/update', 'InvoicePatternsController@update')->name('invoice-patterns.update')->middleware('permission:invoice-patterns.edit');
    Route::delete('invoice-patterns/{pattern}', 'InvoicePatternsController@destroy')->name('invoice-patterns.destroy')->middleware('permission:invoice-patterns.destroy');
    Route::get('invoice-patterns/status/{id}/{status}', 'InvoicePatternsController@status')->name('invoice-patterns.status')->middleware('permission:invoice-patterns.edit');

    // Invoice Settings:
    Route::get('invoice-settings', 'InvoiceSettingsController@index')->name('invoice-settings.index')->middleware('permission:invoices.edit');
    Route::get('invoice-settings/create', 'InvoiceSettingsController@create')->name('invoice-settings.create')->middleware('permission:invoices.edit');
    Route::post('invoice-settings/store', 'InvoiceSettingsController@store')->name('invoice-settings.store')->middleware('permission:invoices.edit');
    Route::get('invoice-settings/{template}/edit', 'InvoiceSettingsController@edit')->name('invoice-settings.edit')->middleware('permission:invoices.edit');
    Route::put('invoice-settings/{template}/update', 'InvoiceSettingsController@update')->name('invoice-settings.update')->middleware('permission:invoices.edit');
    Route::get('invoice-settings/status/{template}/{status}', 'InvoiceSettingsController@status')->name('invoice-settings.status');
    Route::delete('invoice-settings/{template}', 'InvoiceSettingsController@destroy')->name('invoice-settings.destroy')->middleware('permission:invoice-settings.destroy');
    Route::get('invoice-settings/{template}/featured', 'InvoiceSettingsController@featured')->name('invoice-settings.featured')->middleware('permission:invoices.edit');

    // Invoices:
    Route::get('invoices/list/{year?}/{month?}/{company?}', 'InvoiceController@index')->name('invoices.index')->middleware('permission:invoices.index');
    Route::post('invoices', 'InvoiceController@getInvoicesBy')->name('invoices.list')->middleware('permission:invoices.index');
    Route::post('invoices/create-from-order', 'InvoiceController@createFromOrder')->name('invoices.create-from-order')->middleware('permission:invoices.create');
    Route::get('invoices/create-from-provider/{project_id?}/{order_id?}', 'InvoiceController@createFromProvider')->name('invoices.create-from-provider')->middleware('permission:invoices.create');
    Route::post('invoices/store-from-provider', 'InvoiceController@storeFromProvider')->name('invoices.store-from-provider')->middleware('permission:invoices.create');
    Route::get('invoices/{invoice}/edit/{tab?}', 'InvoiceController@edit')->name('invoices.edit')->middleware('permission:invoices.edit');
    Route::get('invoices/{invoice}/edit-purchase/{tab?}', 'InvoiceController@editPurchase')->name('invoices.edit-purchase')->middleware('permission:invoices.edit');
    Route::get('invoices/{invoice}/show', 'InvoiceController@show')->name('invoices.show');
    Route::put('invoices/{invoice}/update', 'InvoiceController@update')->name('invoices.update')->middleware('permission:invoices.edit');
    Route::get('invoices/{invoice}/print/{bn?}', 'InvoiceController@print')->name('invoices.print');
    Route::get('invoices/{invoice}/status/{status}', 'InvoiceController@status')->name('invoices.status');
    Route::get('invoices/{invoice}/block/{return?}', 'InvoiceController@block')->name('invoices.block')->middleware('permission:invoices.edit');
    Route::get('invoices/{invoice}/unlock/{return?}', 'InvoiceController@unlock')->name('invoices.unlock')->middleware('permission:invoices.edit');
    Route::delete('invoices/{invoice}', 'InvoiceController@destroy')->name('invoices.destroy')->middleware('permission:invoices.destroy');
    Route::put('invoices/{invoice}/update-template', 'InvoiceController@updateTemplate')->name('invoices.update-template')->middleware('permission:invoices.edit');
    Route::get('invoices/{order}/by-order', 'InvoiceController@invoicesByOrder')->name('invoices.by-order');
    Route::get('invoices/{invoice}/with_iva/{status}', 'InvoiceController@withIva')->name('invoices.with-iva')->middleware('permission:invoices.edit');
    Route::post('invoices/create-from-group', 'InvoiceController@createFromGroup')->name('invoices.create-from-group')->middleware('permission:invoices.create');
    Route::get('invoices/{order}/by-order-group', 'InvoiceController@invoicesByOrderGroup')->name('invoices.by-order-group');
    Route::post('invoices/refund', 'InvoiceController@refund')->name('invoices.refund')->middleware('permission:invoices.edit');
    Route::get('invoices/{invoice}/remove-from-invoice/{order}', 'InvoiceController@removeFromInvoice')->name('invoices.remove-from-invoice')->middleware('permission:invoices.edit');
    Route::get('invoices/control', 'InvoiceController@control')->name('invoices.control')->middleware('permission:invoices.index');
    Route::get('invoices/{invoice}/delete-doc', 'InvoiceController@removeDoc')->name('invoices.delete-doc')->middleware('permission:invoices.edit');

    // IP Access:
    Route::get('ip-access', 'IpAccessController@index')->name('ip-access.index');
    Route::post('ip-access/store', 'IpAccessController@store')->name('ip-access.store');
    Route::delete('ip-access/{range}', 'IpAccessController@destroy')->name('ip-access.destroy');

    // Item Status:
    Route::get('item-status/{id}', 'ItemStatusController@index')->name('item-status.index');
    Route::post('item-status/store', 'ItemStatusController@store')->name('item-status.store');
    Route::get('item-status/{id}/delete', 'ItemStatusController@destroy')->name('item-status.delete');
    Route::get('item-status/{item}/{status}/status', 'ItemStatusController@status')->name('item-status.status');

    // Items:
    Route::delete('items/{item}', 'ItemController@destroy')->name('items.destroy')->middleware('permission:orders.edit');
    Route::get('items/{item}/copy', 'ItemController@copy')->name('items.copy')->middleware('permission:budgets.edit');
    Route::get('items/{order_id}/copy-block/{block_id}', 'ItemController@copyBlock')->name('items.copy-block')->middleware('permission:budgets.edit');
    Route::post('items/store', 'ItemController@store')->name('items.store');
    Route::get('items/{item}/edit', 'ItemController@edit')->name('items.edit');
    Route::post('items/update', 'ItemController@update')->name('items.update');
    Route::delete('items/{item}', 'ItemController@destroy')->name('items.destroy')->middleware('permission:orders.edit');
    Route::post('items/import-items', 'ItemController@importOrderItems')->name('items.import-items')->middleware('permission:orders.edit');
    Route::get('items/{item}/status/{status}', 'ItemController@status')->name('items.status')->middleware('permission:orders.edit');

    // IVA Types:
    Route::get('iva-types', 'IvaTypeController@index')->name('iva-types.index')->middleware('permission:iva-types.index');
    Route::post('iva-types/store', 'IvaTypeController@store')->name('iva-types.store')->middleware('permission:iva-types.create');
    Route::post('iva-types/update', 'IvaTypeController@update')->name('iva-types.update')->middleware('permission:iva-types.update');
    Route::delete('iva-types/{type}', 'IvaTypeController@destroy')->name('iva-types.destroy')->middleware('permission:iva-types.edit');
    Route::get('iva-types/status/{id}/{status}', 'IvaTypeController@status')->name('iva-types.status')->middleware('permission:iva-types.edit');

    // Messages:
    Route::get('messages/create', 'MessageController@create')->name('messages.create');
    Route::post('messages/store', 'MessageController@store')->name('messages.store');
    Route::get('messages/{message}/show', 'MessageController@show')->name('messages.show');

    // Migrations Data:
    //  Route::get('data', 'MigrationsDataController@index')->name('data.index')->middleware('permission:data.index');

    // Modules:
    Route::get('modules', 'ModuleController@index')->name('modules.index')->middleware('permission:modules.index');
    Route::get('modules/create', 'ModuleController@create')->name('modules.create')->middleware('permission:modules.create');
    Route::post('modules/store', 'ModuleController@store')->name('modules.store')->middleware('permission:modules.create');
    Route::get('modules/{slug}/show', 'ModuleController@show')->name('modules.show');
    Route::get('modules/{module}/edit/{tab?}', 'ModuleController@edit')->name('modules.edit')->middleware('permission:modules.edit');
    Route::post('modules/update', 'ModuleController@update')->name('modules.update')->middleware('permission:modules.edit');
    Route::get('modules/status/{id}/{status}', 'ModuleController@status')->name('modules.status')->middleware('permission:modules.edit');
    Route::delete('modules/{module}', 'ModuleController@destroy')->name('modules.destroy')->middleware('permission:modules.destroy');

    // Notes:
    Route::post('notes/store', 'NoteController@store')->name('notes.store');

    // Notifications:
    Route::get('notifications/{tab?}', 'NotificationController@index')->name('notifications.index');
    Route::get('notifications-reload', function () {
        return redirect()->back();
    })->name('notifications-reload');
    Route::get('notifications-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    })->name('notifications-read');
    Route::get('notifications/{notification}/show', 'NotificationController@show')->name('notifications.show');
    Route::delete('notifications/{notification}', 'NotificationController@destroy')->name('notifications.destroy');
    Route::get('notifications/status/{notification}/{status}', 'NotificationController@readStatus')->name('notifications.status');
    Route::get('new-notifications', 'NotificationController@newNotifications')->name('new-notifications');

    // Order Docs:
    Route::post('order-docs/{order}/store', 'OrderDocsController@store')->name('order-docs.store');
    Route::get('orders-docs/{doc}/destroy', 'OrderDocsController@destroy')->name('orders-docs.destroy')->middleware('permission:order-docs.destroy');

    // Order Groups:
    Route::get('order-groups/list/{customer_id?}/{year?}', 'OrderGroupsController@index')->name('order-groups.index')->middleware('permission:orders.edit');
    Route::post('order-groups', 'OrderGroupsController@getGroupsBy')->name('order-groups.list')->middleware('permission:orders.edit');
    Route::get('order-groups/create/{customer_id?}', 'OrderGroupsController@create')->name('order-groups.create')->middleware('permission:orders.create');
    Route::post('order-groups/add-orders', 'OrderGroupsController@addOrders')->name('order-groups.add-orders')->middleware('permission:orders.edit');
    Route::post('order-groups/add-order', 'OrderGroupsController@addOrder_')->name('order-groups.add-order')->middleware('permission:orders.edit');
    Route::delete('order-groups/{order}', 'OrderGroupsController@destroy')->name('order-groups.destroy')->middleware('permission:orders.edit');
    Route::get('order-groups/{order}/to-invoice', 'OrderGroupsController@toInvoice')->name('order-groups.to-invoice')->middleware('permission:invoices.create');
    Route::get('order-groups/{order}/print', 'OrderGroupsController@print')->name('order-groups.print');

    // Order Patterns:
    Route::get('order-patterns', 'OrderPatternsController@index')->name('order-patterns.index')->middleware('permission:orders.edit');
    Route::get('order-patterns/create', 'OrderPatternsController@create')->name('order-patterns.create')->middleware('permission:orders.edit');
    Route::post('order-patterns/store', 'OrderPatternsController@store')->name('order-patterns.store')->middleware('permission:orders.edit');
    Route::get('order-patterns/{pattern}/edit', 'OrderPatternsController@edit')->name('order-patterns.edit')->middleware('permission:orders.edit');
    Route::delete('order-patterns/{pattern}', 'OrderPatternsController@destroy')->name('order-patterns.destroy')->middleware('permission:orders.destroy');
    Route::put('order-patterns/{pattern}/update', 'OrderPatternsController@update')->name('order-patterns.update')->middleware('permission:orders.edit');
    Route::get('order-patterns/status/{id}/{status}', 'OrderPatternsController@status')->name('order-patterns.status')->middleware('permission:order-patterns.edit');

    // Order Previsions:
    Route::get('order-prevision/{prevision}/delete', 'OrderPrevisionsController@destroy')->name('order-prevision.delete')->middleware('permission:orders.edit');
    Route::get('order-previsions/{order}/recalculate-previsions', 'OrderPrevisionsController@recalculatePrevisions')->name('order-previsions.recalculate-previsions')->middleware('permission:order-prevision.edit');

    // Order Settings:
    Route::get('order-settings', 'OrderSettingsController@index')->name('order-settings.index');
    Route::get('order-settings/create', 'OrderSettingsController@create')->name('order-settings.create')->middleware('permission:order-settings.edit');
    Route::post('order-settings/store', 'OrderSettingsController@store')->name('order-settings.store')->middleware('permission:order-settings.edit');
    Route::get('order-settings/{template}/edit', 'OrderSettingsController@edit')->name('order-settings.edit')->middleware('permission:order-settings.edit');
    Route::delete('order-settings/{template}', 'OrderSettingsController@destroy')->name('order-settings.destroy')->middleware('permission:order-settings.destroy');
    Route::put('order-settings/{template}/update', 'OrderSettingsController@update')->name('order-settings.update')->middleware('permission:orders.edit');
    Route::get('order-settings/status/{template}/{status}', 'OrderSettingsController@status')->name('order-settings.status');
    Route::get('order-settings/{template}/featured', 'OrderSettingsController@featured')->name('order-settings.featured')->middleware('permission:order-settings.edit');

    // Orders:
    Route::get('orders/list/{year?}/{customer_id?}/{provider_id?}/{owner?}/{status?}/{from?}/{to?}/{order_buy?}', 'OrderController@index')->name('orders.index')->middleware('permission:orders.index');
    Route::post('orders', 'OrderController@getOrdersBy')->name('orders.list')->middleware('permission:orders.index');
    Route::get('orders/control-panel-sales/{year?}/{workplace_id?}', 'OrderController@controlPanelSales')->name('orders.control-panel-sales')->middleware('permission:orders.index');
    Route::get('orders/control-panel-purchases/{year?}/{workplace_id?}', 'OrderController@controlPanelPurchases')->name('orders.control-panel-purchases')->middleware('permission:orders.index');
    Route::get('orders/{order}/order-from-budget', 'OrderController@orderFromBudget')->name('orders.order-from-budget')->middleware('permission:orders.create');
    Route::get('orders/{product_provider}/order-from-product', 'OrderController@orderFromProduct')->name('orders.order-from-product')->middleware('permission:orders.create');
    Route::get('orders/{type}/create/{company_id?}', 'OrderController@create')->name('orders.create')->middleware('permission:orders.create');
    Route::post('orders/store', 'OrderController@store')->name('orders.store')->middleware('permission:orders.create');
    Route::get('orders/{order}/edit/{tab?}', 'OrderController@edit')->name('orders.edit')->middleware('permission:orders.edit');
    Route::get('orders/{order}/print', 'OrderController@print')->name('orders.print')->middleware('permission:orders.edit');
    Route::get('orders/{order}/show', 'OrderController@show')->name('orders.show')->middleware('permission:orders.show');
    Route::put('orders/{order}/update', 'OrderController@update')->name('orders.update')->middleware('permission:orders.edit');
    Route::post('orders/export-by-range-dates', 'OrderController@exportOrdersByDateRange')->name('orders.export-by-range-dates');
    Route::get('orders/{order}/reset-num', 'OrderController@resetNum')->name('orders.reset-num')->middleware('permission:orders.edit');
    Route::delete('orders/{order}', 'OrderController@destroy')->name('orders.destroy')->middleware('permission:orders.destroy');
    Route::get('orders/{order}/status/{status}', 'OrderController@status')->name('orders.status');
    Route::post('orders/add-purchase-to-project', 'OrderController@addPurchaseToProject')->name('orders.add-purchase-to-project')->middleware('permission:projects.edit');
    Route::delete('purchase-from-project/{purchase}', 'OrderController@destroyPurchaseToProject')->name('purchase-from-project.destroy')->middleware('permission:projects.edit');
    Route::get('orders/{order}/set-delivery', 'OrderController@setOrderDelivery')->name('orders.set-delivery')->middleware('permission:deliveries.create');
    Route::put('orders/{order}/update-template', 'OrderController@updateTemplate')->name('orders.update-template')->middleware('permission:orders.edit');
    Route::get('orders/{order}/remove-template', 'OrderController@removeTemplate')->name('orders.remove-template')->middleware('permission:orders.edit');
    Route::put('orders/{order}/add-discount', 'OrderController@addDiscount')->name('orders.add-discount')->middleware('permission:budgets.edit');
    Route::post('orders/{order}/set-customer', 'OrderController@setCustomer')->name('orders.set-customer')->middleware('permission:orders.edit');
    Route::get('orders/{order}/remove-discount/{return?}', 'OrderController@removeDiscount')->name('orders.remove-discount')->middleware('permission:orders.edit');
    Route::post('orders/{order}/add-member', 'OrderController@addTeamMember')->name('orders.add-member')->middleware('permission:orders.edit');
    Route::delete('orders/{order}/remove-member', 'OrderController@removeTeamMember')->name('orders.remove-member')->middleware('permission:orders.edit');
    Route::get('orders/{order}/remove-purchase-doc', 'OrderController@removePurchaseDoc')->name('orders.remove-purchase-doc')->middleware('permission:orders.edit');
    Route::get('orders/{provider_id}/orders-about-providers', 'OrderController@aboutProviders')->name('orders.orders-about-providers');
    Route::get('orders/{cash}/order-from-cash', 'OrderController@orderFromCash')->name('orders.order-from-cash')->middleware('permission:orders.create');

    Route::get('control-panel', 'OrderController@index')->name('control-panel.index')->middleware('permission:orders.index');
    Route::post('orders/control-panel-sales-year', 'OrderController@controlPanelSalesPost')->name('orders.control-panel-sales-year')->middleware('permission:orders.index');
    Route::post('orders/control-panel-purchases-year', 'OrderController@controlPanelPurchasesPost')->name('orders.control-panel-purchases-year')->middleware('permission:orders.index');


    Route::get('orders/create/{project_id?}', 'OrderController@create')->name('orders-forms.create')->middleware('permission:orders.create');

    // Payment Methods:
    Route::get('payment-methods', 'PaymentMethodController@index')->name('payment-methods.index')->middleware('permission:payment-methods.index');
    Route::get('payment-methods/create', 'PaymentMethodController@create')->name('payment-methods.create')->middleware('permission:payment-methods.create');
    Route::post('payment-methods/store', 'PaymentMethodController@store')->name('payment-methods.store')->middleware('permission:payment-methods.create');
    Route::get('payment-methods/{method}/edit', 'PaymentMethodController@edit')->name('payment-methods.edit')->middleware('permission:payment-methods.edit');
    Route::put('payment-methods/{method}/update', 'PaymentMethodController@update')->name('payment-methods.update')->middleware('permission:payment-methods.edit');
    Route::get('payment-methods/status/{id}/{status}', 'PaymentMethodController@status')->name('payment-methods.status')->middleware('permission:payment-methods.edit');
    Route::delete('payment-methods/{method}', 'PaymentMethodController@destroy')->name('payment-methods.destroy')->middleware('permission:payment-methods.destroy');

    // Payments:
    Route::post('payments/store', 'PaymentController@store')->name('payments.store')->middleware('permission:invoices.edit');
    Route::post('payments/update', 'PaymentController@update')->name('payments.update')->middleware('permission:invoices.edit');
    Route::delete('payments/{payment}', 'PaymentController@destroy')->name('payments.destroy')->middleware('permission:invoices.edit');

    // Permissions:
    Route::resource('permissions', 'PermissionController', ['except' => ['update', 'create', 'show']]);
    Route::get('permissions/{user}/user', 'PermissionController@userPermissions')->name('permissions.user')->middleware('permission:permissions.edit');
    Route::post('permissions/{user}/update-user-permissions', 'PermissionController@updateUserPermissions')->name('permissions.update-user-permissions')->middleware('permission:permissions.edit');
    Route::get('permissions/{slug}/functionalities', 'PermissionController@getFuncionalityPermissions')->name('permissions.functionalities');
    Route::get('permissions/{permission_id}/set-permission/{user}/{checked}', 'PermissionController@setUserPermission')->name('permissions.set-permission');
    Route::get('permissions/{permission_id}/set-all-permissions/{user}/{checked}', 'PermissionController@setAllUserPermissions')->name('permissions.set-all-permissions');

    // Phones:
    Route::post('phones/store', 'PhoneController@store')->name('phones.store');
    Route::get('phones/{phone}/delete', 'PhoneController@destroy')->name('phones.delete');
    Route::post('phones/store-from-user', 'PhoneController@storeFromUser')->name('phones.store-from-user');

    // Planning:
    Route::get('planning', 'PlanningController@index')->name('planning.index')->middleware('permission:planning.index');
    Route::get('planning/create', 'PlanningController@create')->name('planning.create')->middleware('permission:planning.create');

    // Preferences:
    Route::post('preferences/store', 'PreferenceController@store')->name('preferences.store');
    Route::get('preferences/destroy/{preference}', 'PreferenceController@destroy')->name('preferences.destroy');

    // Procedure Patterns:
    Route::get('procedure-patterns', 'ProcedurePatternsController@index')->name('procedure-patterns.index')->middleware('permission:procedure-patterns.index');
    Route::get('procedure-patterns/create', 'ProcedurePatternsController@create')->name('procedure-patterns.create')->middleware('permission:procedure-patterns.create');
    Route::post('procedure-patterns/store', 'ProcedurePatternsController@store')->name('procedure-patterns.store')->middleware('permission:procedure-patterns.create');
    Route::get('procedure-patterns/{pattern}/edit', 'ProcedurePatternsController@edit')->name('procedure-patterns.edit')->middleware('permission:procedures.edit');
    Route::delete('procedure-patterns/{pattern}', 'ProcedurePatternsController@destroy')->name('procedure-patterns.destroy')->middleware('permission:procedures.edit');
    Route::post('procedure-patterns/{pattern}/update', 'ProcedurePatternsController@update')->name('procedure-patterns.update')->middleware('permission:procedures.edit');
    Route::get('procedure-patterns/status/{id}/{status}', 'ProcedurePatternsController@status')->name('procedure-patterns.status')->middleware('permission:procedure-patterns.edit');

    // Procedure Revisions:
    Route::get('procedure-revisions', 'ProcedureRevisionsController@index')->name('procedure-revisions.index')->middleware('permission:procedure-revisions.index');
    Route::post('procedure-revisions/store', 'ProcedureRevisionsController@store')->name('procedure-revisions.store')->middleware('permission:procedures.edit');
    Route::get('procedure-revisions/{revision}/print', 'ProcedureRevisionsController@print')->name('procedure-revisions.print');

    // Procedures:
    Route::get('procedures', 'ProcedureController@index')->name('procedures.index')->middleware('permission:procedures.index');
    Route::get('procedures/create', 'ProcedureController@create')->name('procedures.create')->middleware('permission:procedures.create');
    Route::post('procedures/store', 'ProcedureController@store')->name('procedures.store')->middleware('permission:procedures.create');
    Route::get('procedures/{procedure}/edit/{tab?}/{revision?}', 'ProcedureController@edit')->name('procedures.edit')->middleware('permission:procedures.edit');
    Route::put('procedures/{procedure}/update', 'ProcedureController@update')->name('procedures.update')->middleware('permission:procedures.edit');
    Route::delete('procedures/{procedure}', 'ProcedureController@destroy')->name('procedures.destroy')->middleware('permission:procedures.destroy');

    // Product Attributes:
    Route::post('product-attributes/store', 'ProductAttributesController@store')->name('product-attributes.store')->middleware('permission:products.edit');
    Route::post('product-attributes/update', 'ProductAttributesController@update')->name('product-attributes.update')->middleware('permission:products.edit');
    Route::delete('product-attributes/{attribute}', 'ProductAttributesController@destroy')->name('product-attributes.destroy')->middleware('permission:products.edit');

    // Product Categories:
    Route::get('product-categories/store/{product_id}/{category_id}', 'ProductCategoriesController@store')->name('product-categories.store');

    // Product Docs:
    Route::post('product-docs/{product}/store', 'ProductDocsController@store')->name('product-docs.store');
    Route::get('product-docs/{doc}/destroy', 'ProductDocsController@destroy')->name('product-docs.destroy')->middleware('permission:product-docs.destroy');
    Route::get('product-docs/set-featured/{product}/{doc}', 'ProductDocsController@setFeatured')->name('product-docs.set-featured')->middleware('permission:products.edit');

    // Product Favorites:
    Route::get('product-favorites/{product_id}/add-remove', 'ProductFavoritesController@add_remove')->name('product-favorites.add-remove');

    // Product Patterns:
    Route::get('product-patterns', 'ProductPatternsController@index')->name('product-patterns.index')->middleware('permission:product-patterns.index');
    Route::get('product-patterns/create', 'ProductPatternsController@create')->name('product-patterns.create')->middleware('permission:product-patterns.create');
    Route::post('product-patterns/store', 'ProductPatternsController@store')->name('product-patterns.store')->middleware('permission:product-patterns.create');
    Route::get('product-patterns/{pattern}/edit', 'ProductPatternsController@edit')->name('product-patterns.edit')->middleware('permission:products.edit');
    Route::delete('product-patterns/{pattern}', 'ProductPatternsController@destroy')->name('product-patterns.destroy')->middleware('permission:products.edit');
    Route::post('product-patterns/{pattern}/update', 'ProductPatternsController@update')->name('product-patterns.update')->middleware('permission:products.edit');
    Route::get('product-patterns/status/{id}/{status}', 'ProductPatternsController@status')->name('product-patterns.status')->middleware('permission:product-patterns.edit');

    //Product Providers:
    Route::get('product-providers/{product}', 'ProductProvidersController@index')->name('product-providers.index')->middleware('permission:providers.edit');
    Route::get('product-providers/{product}/create/{provider_id?}', 'ProductProvidersController@create')->name('product-providers.create')->middleware('permission:providers.edit');
    Route::post('product-providers/{product}/set-provider', 'ProductProvidersController@setProvider')->name('product-providers.set-provider')->middleware('permission:providers.edit');
    Route::post('product-providers/store', 'ProductProvidersController@store')->name('product-providers.store');
    Route::post('product-providers/store-from-provider', 'ProductProvidersController@storeFromProvider')->name('product-providers.store-from-provider');
    Route::get('product-providers/{product_id}/show', 'ProductProvidersController@show')->name('product-providers.show');
    Route::delete('product-providers/{provider}', 'ProductProvidersController@destroy')->name('product-providers.destroy')->middleware('permission:products.edit');

    // Product Serials:
    Route::post('product-serials/{product}/store', 'ProductSerialsController@store')->name('product-serials.store')->middleware('permission:products.edit');
    Route::delete('product-serials/{serial}', 'ProductSerialsController@destroy')->name('product-serials.destroy')->middleware('permission:products.edit');

    // Product Serials Cards:
    Route::get('product-serials-cards/{serial}/edit', 'ProductSerialsCardsController@edit')->name('product-serials-cards.edit');
    Route::post('products-serials-cards/update', 'ProductSerialsCardsController@update')->name('products-serials-cards.update');
    Route::delete('product-serials-cards/{serial}', 'ProductSerialsCardsController@destroy')->name('product-serials-cards.destroy');

    // Product Units:
    Route::get('product-units/status/{product_id}/{unit_id}', 'ProductUnitsController@status')->name('product-units.status')->middleware('permission:product-units.edit');

    // Products:
    Route::get('products', 'ProductController@index')->name('products.index');
    Route::get('products/create/{company_id?}', 'ProductController@create')->name('products.create')->middleware('permission:products.create');
    Route::post('products/store', 'ProductController@store')->name('products.store')->middleware('permission:products.create');
    Route::get('products/{product}/edit/{tab?}', 'ProductController@edit')->name('products.edit')->middleware('permission:products.edit');
    Route::get('products/{product}/show', 'ProductController@show')->name('products.show');
    Route::put('products/{product}/update', 'ProductController@update')->name('products.update')->middleware('permission:products.edit');
    Route::get('products/status/{id}/{status}', 'ProductController@status')->name('products.status')->middleware('permission:products.edit');
    Route::delete('products/{product}', 'ProductController@destroy')->name('products.destroy')->middleware('permission:products.destroy');
    Route::get('products/{product_id}/purchase-orders', 'ProductController@purchaseOrders')->name('products.purchase-orders');

    // Project Docs:
    Route::post('project-docs/{project}/store', 'ProjectDocsController@store')->name('project-docs.store');
    Route::get('project-docs/set-featured/{question}/{image}', 'ProjectDocsController@setFeatured')->name('project-docs.set-featured')->middleware('permission:projects.edit');
    Route::get('project-docs/set-thumb/{project}/{image}', 'ProjectDocsController@setThumb')->name('project-docs.set-thumb')->middleware('permission:projects.edit');
    Route::get('project-docs/{doc}/destroy', 'ProjectDocsController@destroy')->name('project-docs.destroy')->middleware('permission:projects.edit');

    // Project Favourites:
    Route::get('project-favourites/status/{project}/{status}', 'ProjectFavouriteController@status')->name('project-favourites.status');
    Route::get('project-favourites/{project_id}/delete', 'ProjectFavouriteController@delete')->name('project-favourites.delete');

    // Project Groups:
    Route::post('project-groups/store', 'ProjectGroupController@store')->name('project-groups.store')->middleware('permission:projects.edit');

    // Project Orders:
    Route::post('project-orders/store', 'ProjectOrdersController@store')->name('project-orders.store')->middleware('permission:projects.edit');

    // Project Patterns:
    Route::get('project-patterns', 'ProjectPatternsController@index')->name('project-patterns.index')->middleware('permission:project-patterns.index');
    Route::get('project-patterns/create', 'ProjectPatternsController@create')->name('project-patterns.create')->middleware('permission:project-patterns.create');
    Route::post('project-patterns/store', 'ProjectPatternsController@store')->name('project-patterns.store')->middleware('permission:project-patterns.create');
    Route::get('project-patterns/{pattern}/edit', 'ProjectPatternsController@edit')->name('project-patterns.edit')->middleware('permission:projects.edit');
    Route::delete('project-patterns/{pattern}', 'ProjectPatternsController@destroy')->name('project-patterns.destroy')->middleware('permission:projects.edit');
    Route::post('project-patterns/{pattern}/update', 'ProjectPatternsController@update')->name('project-patterns.update')->middleware('permission:projects.edit');
    Route::get('project-patterns/status/{id}/{status}', 'ProjectPatternsController@status')->name('project-patterns.status')->middleware('permission:project-patterns.edit');

    // Project Purchases:
    Route::post('project-purchases/store', 'ProjectPurchasesController@store')->name('project-purchases.store')->middleware('permission:projects.edit');

    // Projects:
    Route::get('projects', 'ProjectController@index')->name('projects.index');
    Route::get('projects/create', 'ProjectController@create')->name('projects.create');
    Route::post('projects/store', 'ProjectController@store')->name('projects.store')->middleware('permission:projects.create');
    Route::get('projects/{project}/edit/{tab?}', 'ProjectController@edit')->name('projects.edit');
    Route::put('projects/{project}/update', 'ProjectController@update')->name('projects.update');
    Route::get('projects/{order}/create-from-budget-order', 'ProjectController@createFromBudgetOrder')->name('projects.create-from-budget-order')->middleware('permission:projects.create');
    Route::get('projects/{project}/status/{status}', 'ProjectController@status')->name('projects.status');
    Route::delete('projects/{project}', 'ProjectController@destroy')->name('projects.destroy')->middleware('permission:projects.destroy');
    Route::post('projects/update-owner', 'ProjectController@updateOwner')->name('projects.update-owner')->middleware('permission:projects.edit');
    Route::get('projects/{project}/delete-member/{user_id}', 'ProjectController@deleteMember')->name('projects.delete-member')->middleware('permission:projects.edit');
    Route::post('projects', 'ProjectController@getProjectsBy')->name('projects.list');
    Route::post('projects/update-team', 'ProjectController@updateTeam')->name('projects.update-team')->middleware('permission:projects.edit');
    Route::post('projects/link-project', 'ProjectController@linkProjectToBudgetOrder')->name('projects.link-project');
    Route::get('projects/{project}/reset-num', 'ProjectController@resetNum')->name('projects.reset-num')->middleware('permission:projects.edit');
    Route::get('company-projects/{company_id}', 'ProjectController@companyProjects')->name('company-projects');

    // Protocol Items:
    Route::get('protocol-items/create/{protocol}', 'ProtocolItemController@create')->name('protocol-items.create');
    Route::post('protocol-items/store', 'ProtocolItemController@store')->name('protocol-items.store')->middleware('permission:protocols.edit');
    Route::get('protocol-items/{item}/edit', 'ProtocolItemController@edit')->name('protocol-items.edit');
    Route::put('protocol-items/{item}/update', 'ProtocolItemController@update')->name('protocol-items.update');
    Route::get('protocol-items/{item}/status/{status}', 'ProtocolItemController@status')->name('protocol-items.status');
    Route::delete('protocol-items/{item}', 'ProtocolItemController@destroy')->name('protocol-items.destroy')->middleware('permission:protocols.edit');
    Route::post('protocol-items/reorder-items', 'ProtocolItemController@reorderFilesDT')->name('protocol-items.reorder-items');

    // Protocol Users:
    Route::post('protocol-users/store', 'ProtocolUserController@store')->name('protocol-users.store')->middleware('permission:protocols.edit');
    Route::delete('protocol-users/{user}', 'ProtocolUserController@destroy')->name('protocol-users.destroy')->middleware('permission:protocols.edit');
    Route::post('protocol-users/sign', 'ProtocolUserController@sign')->name('protocol-users.sign');

    // Protocols:
    Route::get('protocols', 'ProtocolController@index')->name('protocols.index')->middleware('permission:protocols.index');
    Route::get('protocols/create', 'ProtocolController@create')->name('protocols.create');
    Route::post('protocols/store', 'ProtocolController@store')->name('protocols.store')->middleware('permission:protocols.create');
    Route::get('protocols/{protocol}/edit/{tab?}', 'ProtocolController@edit')->name('protocols.edit');
    Route::get('protocols/{protocol}/show', 'ProtocolController@show')->name('protocols.show');
    Route::put('protocols/{protocol}/update', 'ProtocolController@update')->name('protocols.update');
    Route::get('protocols/{protocol}/status/{status}', 'ProtocolController@status')->name('protocols.status');
    Route::delete('protocols/{protocol}', 'ProtocolController@destroy')->name('protocols.destroy')->middleware('permission:protocols.destroy');
    Route::get('protocols/{protocol}/print', 'ProtocolController@print')->name('protocols.print');

    // Provider Products:
    Route::get('provider-products/{cp}', 'CustomerProductsController@providerCatalogue')->name('provider-products.index');

    // Providers:
    Route::get('providers', 'CustomerProvidersController@providers')->name('providers.index');
    Route::get('providers/create', 'CustomerProvidersController@createProvider')->name('providers.create')->middleware('permission:providers.create');
    Route::get('providers/{company}/edit/{tab?}', 'CustomerProvidersController@editProvider')->name('providers.edit')->middleware('permission:providers.edit');
    Route::get('providers/{cp}/show', 'CustomerProvidersController@show')->name('providers.show');
    Route::get('providers/import', 'CustomerProvidersController@importForm')->name('providers.import')->middleware('permission:providers.edit');
    Route::post('providers/export', 'CustomerProvidersController@exportProviders')->name('providers.export')->middleware('permission:providers.edit');
    Route::get('providers/{company}/pay-conditions-provider', 'CustomerProvidersController@payConditionsProvider')->name('providers.pay-conditions-provider')->middleware('permission:providers.edit');

    // Provinces:
    Route::get('provinces/{country}', 'ProvinceController@index')->name('provinces.index');
    Route::post('provinces/store', 'ProvinceController@store')->name('provinces.store')->middleware('permission:countries.edit');
    Route::get('provinces/status/{id}/{status}', 'ProvinceController@status')->name('provinces.status')->middleware('permission:countries.edit');
    Route::post('provinces/update', 'ProvinceController@update')->name('provinces.update')->middleware('permission:countries.edit');
    Route::delete('provinces/{province}', 'ProvinceController@destroy')->name('provinces.destroy')->middleware('permission:countries.edit');

    // Purchase Patterns:
    Route::get('purchase-patterns', 'PurchasePatternsController@index')->name('purchase-patterns.index')->middleware('permission:purchase-patterns.index');
    Route::get('purchase-patterns/create', 'PurchasePatternsController@create')->name('purchase-patterns.create')->middleware('permission:purchase-patterns.create');
    Route::post('purchase-patterns/store', 'PurchasePatternsController@store')->name('purchase-patterns.store')->middleware('permission:purchase-patterns.create');
    Route::get('purchase-patterns/{pattern}/edit', 'PurchasePatternsController@edit')->name('purchase-patterns.edit')->middleware('permission:purchases.edit');
    Route::delete('purchase-patterns/{pattern}', 'PurchasePatternsController@destroy')->name('purchase-patterns.destroy')->middleware('permission:purchases.destroy');
    Route::put('purchase-patterns/{pattern}/update', 'PurchasePatternsController@update')->name('purchase-patterns.update')->middleware('permission:purchases.edit');
    Route::get('purchase-patterns/status/{id}/{status}', 'PurchasePatternsController@status')->name('purchase-patterns.status')->middleware('permission:purchase-patterns.edit');

    // Question Images:
    Route::post('question-images/{question}/store', 'QuestionImagesController@store')->name('question-images.store')->middleware('permission:questions.edit');
    Route::get('question-images/{question}/destroy/{image}', 'QuestionImagesController@destroy')->name('question-images.destroy')->middleware('permission:questions.edit');
    Route::get('question-images/set-featured/{question}/{image}', 'QuestionImagesController@setFeatured')->name('question-images.set-featured')->middleware('permission:questions.edit');
    Route::get('question-images/set-thumb/{question}/{image}', 'QuestionImagesController@setThumb')->name('question-images.set-thumb')->middleware('permission:questions.edit');

    // Questions:
    Route::get('questions', 'QuestionController@index')->name('questions.index')->middleware('permission:questions.index');
    Route::get('questions/create/{quiz?}', 'QuestionController@create')->name('questions.create')->middleware('permission:questions.create');
    Route::post('questions/store', 'QuestionController@store')->name('questions.store')->middleware('permission:questions.edit');
    Route::get('questions/{question}/edit/{tab?}', 'QuestionController@edit')->name('questions.edit')->middleware('permission:questions.edit');
    Route::put('questions/{question}', 'QuestionController@update')->name('questions.update')->middleware('permission:questions.edit');
    Route::delete('questions/{question}', 'QuestionController@destroy')->name('questions.destroy')->middleware('permission:questions.destroy');
    Route::get('questions/status/{id}/{status}', 'QuestionController@status')->name('questions.status')->middleware('permission:questions.edit');
    Route::post('questions/{question}/store-images', 'QuestionController@storeImages')->name('questions.store-images');

    // Quiz Edition Answers:
    Route::post('quiz-edition-answers/store', 'QuizEditionAnswerController@store')->name('quiz-edition-answers.store');

    // Quiz Edition Companies:
    Route::post('quiz-edition-companies/store', 'QuizEditionCompanyController@store')->name('quiz-edition-companies.store')->middleware('permission:quiz-editions.edit');
    Route::delete('quiz-edition-companies/{company}', 'QuizEditionCompanyController@destroy')->name('quiz-edition-companies.destroy')->middleware('permission:quiz-editions.edit');
    Route::get('quiz-edition-companies/status/{id}/{status}', 'QuizEditionCompanyController@status')->name('quiz-edition-companies.status')->middleware('permission:quizzes.edit');

    // Quiz Edition Questions:
    Route::delete('quiz-edition-questions/{edition_question}', 'QuizEditionQuestionsController@destroy')->name('quiz-edition-questions.destroy')->middleware('permission:quiz-editions.edit');
    Route::get('quiz-edition-questions/{edition_id}/store/{question_id}', 'QuizEditionQuestionsController@store')->name('quiz-edition-questions.store');
    Route::get('quiz-edition-questions/{edition}/all-questions/{status}', 'QuizEditionQuestionsController@allQuestions')->name('quiz-edition-questions.all-questions');
    Route::get('quiz-edition-questions/{edition_question}/set-points/{points}', 'QuizEditionQuestionsController@setPoints')->name('quiz-edition-questions.set-points');
    Route::get('quiz-edition-questions/{edition_id}/set-points-all/{points}', 'QuizEditionQuestionsController@setPointsAll')->name('quiz-edition-questions.set-points-all');

    // Quiz Edition Teams:
    Route::post('quiz-edition-teams/store', 'QuizEditionTeamsController@store')->name('quiz-edition-teams.store')->middleware('permission:quiz-editions.edit');
    Route::delete('quiz-edition-teams/{edition_team}', 'QuizEditionTeamsController@destroy')->name('quiz-edition-teams.destroy')->middleware('permission:quiz-editions.edit');

    // Quiz Editions:
    Route::get('quiz-editions', 'QuizEditionsController@index')->name('quiz-editions.index')->middleware('permission:quiz-editions.index');
    Route::get('quiz-editions/{quiz}/create', 'QuizEditionsController@create')->name('quiz-editions.create')->middleware('permission:quiz-editions.create');
    Route::get('quiz-editions/{edition}/edit/{tab?}', 'QuizEditionsController@edit')->name('quiz-editions.edit')->middleware('permission:quiz-editions.edit');
    Route::get('quiz-editions/{edition}/show', 'QuizEditionsController@show')->name('quiz-editions.show');
    Route::get('quiz-editions/{edition}/ranking', 'QuizEditionsController@ranking')->name('quiz-editions.ranking');
    Route::get('quiz-editions/{edition}/status', 'QuizEditionsController@status')->name('quiz-editions.status')->middleware('permission:quiz-editions.edit');
    Route::put('quiz-editions/{edition}', 'QuizEditionsController@update')->name('quiz-editions.update')->middleware('permission:quiz-editions.edit');
    Route::delete('quiz-editions/{edition}', 'QuizEditionsController@destroy')->name('quiz-editions.destroy')->middleware('permission:quiz-editions.destroy');
    Route::get('quiz-editions/{edition}/delete-logo', 'QuizEditionsController@deleteLogo')->name('quiz-editions.delete-logo')->middleware('permission:quiz-editions.edit');

    // Quizzes:
    Route::get('quizzes', 'QuizController@index')->name('quizzes.index')->middleware('permission:quizzes.index');
    Route::get('quizzes/create', 'QuizController@create')->name('quizzes.create')->middleware('permission:quizzes.create');
    Route::post('quizzes/store', 'QuizController@store')->name('quizzes.store')->middleware('permission:quizzes.store');
    Route::get('quizzes/{quiz}/edit/{tab?}', 'QuizController@edit')->name('quizzes.edit')->middleware('permission:quizzes.edit');
    Route::put('quizzes/{quiz}', 'QuizController@update')->name('quizzes.update')->middleware('permission:quizzes.edit');
    Route::delete('quizzes/{quiz}', 'QuizController@destroy')->name('quizzes.destroy')->middleware('permission:quizzes.destroy');
    Route::get('quizzes/status/{id}/{status}', 'QuizController@status')->name('quizzes.status')->middleware('permission:quizzes.edit');
    Route::put('quizzes/{quiz}/add-managers', 'QuizController@addManagers')->name('quizzes.add-managers')->middleware('permission:quizzes.edit');
    Route::get('quizzes/{quiz}/delete-manager/{manager}', 'QuizController@deleteManager')->name('quizzes.delete-manager')->middleware('permission:quizzes.edit');

    // Receiving:
    Route::get('receiving', 'ReceivingController@index')->name('receiving.index');
    Route::put('receiving/{order}', 'ReceivingController@store')->name('receiving.store')->middleware('permission:orders.edit');
    Route::get('receiving/{delivery}/all-order/{order}', 'ReceivingController@storeAllOrder')->name('receiving.all-order')->middleware('permission:receiving.edit');
    Route::get('receiving/{delivery}/edit/{tab?}', 'ReceivingController@edit')->name('receiving.edit')->middleware('permission:receiving.edit');
    Route::put('receiving/{delivery}/update', 'ReceivingController@update')->name('receiving.update')->middleware('permission:receiving.edit');

    // Roles:
    Route::get('roles', 'RoleController@index')->name('roles.index')->middleware('permission:roles.index');
    Route::get('roles/create', 'RoleController@create')->name('roles.create')->middleware('permission:roles.create');
    Route::post('roles/store', 'RoleController@store')->name('roles.store')->middleware('permission:roles.create');
    Route::get('roles/{role}/edit', 'RoleController@edit')->name('roles.edit')->middleware('permission:roles.edit');
    Route::put('roles/{role}', 'RoleController@update')->name('roles.update')->middleware('permission:roles.edit');
    Route::delete('roles/{role}', 'RoleController@destroy')->name('roles.destroy')->middleware('permission:roles.destroy');
    Route::get('roles/get-slug/{texto}', 'RoleController@create_slug')->name('roles.get-slug');
    Route::post('roles/copy', 'RoleController@copy')->name('roles.copy')->middleware('permission:roles.create');
    Route::get('roles/sync', 'RoleController@sync')->name('roles.sync')->middleware('permission:roles.create');
    Route::get('roles/{role}/set-permission/{permission}/{action}/{module_on}/{remove_module}', 'RoleController@setPermission')->name('roles.set-permission')->middleware('permission:roles.edit');
    Route::get('roles/{role}/set-functionality-permissions/{functionality}/{action}/{module_on}/{remove_module}', 'RoleController@setFunctionalityPermissions')->name('roles.set-permission')->middleware('permission:roles.edit');

    // SAT:
    Route::get('sat', 'SatController@index')->name('sat.index')->middleware('permission:sat.index');
    Route::get('sat/create', 'SatController@create')->name('sat.create')->middleware('permission:sat.create');
    Route::post('sat/store', 'SatController@store')->name('sat.store')->middleware('permission:sat.create');
    Route::get('sat/{sat}/edit/{tab?}', 'SatController@edit')->name('sat.edit')->middleware('permission:sat.edit');
    Route::put('sat/{sat}/update', 'SatController@update')->name('sat.update')->middleware('permission:sat.edit');
    Route::delete('sat/{sat}', 'SatController@destroy')->name('sat.destroy')->middleware('permission:sat.destroy');
    Route::get('sat/{sat}/status/{status}', 'SatController@status')->name('sat.status');

    // SAT Items:
    Route::post('sat-items/store', 'SatItemController@store')->name('sat-items.store')->middleware('permission:sat.edit');
    Route::get('sat-items/{id}/update-observations/{observations}', 'SatItemController@updateObservations')->name('sat-items.update-observations')->middleware('permission:sat.edit');

    // SAT Patterns:
    Route::get('sat-patterns', 'SatPatternsController@index')->name('sat-patterns.index')->middleware('permission:sat-patterns.index');
    Route::get('sat-patterns/create', 'SatPatternsController@create')->name('sat-patterns.create')->middleware('permission:sat.edit');
    Route::post('sat-patterns/store', 'SatPatternsController@store')->name('sat-patterns.store')->middleware('permission:sat.edit');
    Route::get('sat-patterns/{pattern}/edit', 'SatPatternsController@edit')->name('sat-patterns.edit')->middleware('permission:sat.edit');
    Route::delete('sat-patterns/{pattern}', 'SatPatternsController@destroy')->name('sat-patterns.destroy')->middleware('permission:sat.destroy');
    Route::put('sat-patterns/{pattern}/update', 'SatPatternsController@update')->name('sat-patterns.update')->middleware('permission:sat.edit');
    Route::get('sat-patterns/status/{id}/{status}', 'SatPatternsController@status')->name('sat-patterns.status')->middleware('permission:sat-patterns.edit');

    // Schedule Event Types:
    Route::get('schedule-event-types', 'ScheduleEventTypesController@index')->name('schedule-event-types.index')->middleware('permission:schedules.edit');
    Route::get('schedule-event-types/create', 'ScheduleEventTypesController@create')->name('schedule-event-types.create')->middleware('permission:schedules.edit');
    Route::post('schedule-event-types/store', 'ScheduleEventTypesController@store')->name('schedule-event-types.store');
    Route::get('schedule-event-types/{event_type}/edit', 'ScheduleEventTypesController@edit')->name('schedule-event-types.edit')->middleware('permission:schedule.edit');
    Route::put('schedule-event-types/{event_type}/update', 'ScheduleEventTypesController@update')->name('schedule-event-types.update')->middleware('permission:schedule.edit');
    Route::delete('schedule-event-types/{event_type}', 'ScheduleEventTypesController@destroy')->name('schedule-event-types.destroy')->middleware('permission:schedule.destroy');
    Route::get('schedule-event-types/status/{event_type}/{status}', 'ScheduleEventTypesController@status')->name('schedule-event-types.status')->middleware('permission:schedule.edit');

    // Schedule Events:
    Route::get('schedule-events', 'ScheduleEventsController@index')->name('schedule-events.index');
    Route::post('schedule-events/store', 'ScheduleEventsController@store')->name('schedule-events.store')->middleware('permission:schedules.edit');
    Route::get('schedule-events/{event}/edit', 'ScheduleEventsController@edit')->name('schedule-events.edit');
    Route::post('schedule-events/update', 'ScheduleEventsController@update')->name('schedule-events.update');

    // Schedules:
    Route::get('schedules', 'ScheduleController@index')->name('schedules.index');
    Route::get('schedules/create', 'ScheduleController@create')->name('schedules.create')->middleware('permission:schedules.create');
    Route::post('schedules/store', 'ScheduleController@store')->name('schedules.store')->middleware('permission:schedules.create');
    Route::get('schedules/{schedule}/edit', 'ScheduleController@edit')->name('schedules.edit')->middleware('permission:schedules.edit');
    Route::put('schedules/{schedule}/update', 'ScheduleController@update')->name('schedules.update')->middleware('permission:schedules.edit');
    Route::get('schedules/{schedule}/status/{status}', 'ScheduleController@status')->name('schedules.status')->middleware('permission:schedules.edit');

    // Sectors:
    Route::get('sectors', 'CompanyController@sectors')->name('sectors.index');

    // Shipment Patterns:
    Route::get('shipment-patterns', 'ShipmentPatternsController@index')->name('shipment-patterns.index')->middleware('permission:shipment-patterns.index');
    Route::get('shipment-patterns/create', 'ShipmentPatternsController@create')->name('shipment-patterns.create')->middleware('permission:shipment-patterns.create');
    Route::post('shipment-patterns/store', 'ShipmentPatternsController@store')->name('shipment-patterns.store')->middleware('permission:shipment-patterns.create');
    Route::get('shipment-patterns/{pattern}/edit', 'ShipmentPatternsController@edit')->name('shipment-patterns.edit')->middleware('permission:shipments.edit');
    Route::delete('shipment-patterns/{pattern}', 'ShipmentPatternsController@destroy')->name('shipment-patterns.destroy')->middleware('permission:shipments.destroy');
    Route::put('shipment-patterns/{pattern}/update', 'ShipmentPatternsController@update')->name('shipment-patterns.update')->middleware('permission:shipments.edit');
    Route::get('shipment-patterns/status/{id}/{status}', 'ShipmentPatternsController@status')->name('shipment-patterns.status')->middleware('permission:shipment-patterns.edit');

    // Shipments:
    Route::get('shipments', 'ShipmentController@index')->name('shipments.index');

    // Staff Pick:
    Route::get('staff-pick', 'StaffPickController@index')->name('staff-pick.index')->middleware('permission:staff-pick.index');
    Route::get('staff-pick/create', 'StaffPickController@create')->name('staff-pick.create')->middleware('permission:staff-pick.create');
    Route::get('staff-pick/{process}/edit/{tab?}', 'StaffPickController@edit')->name('staff-pick.edit')->middleware('permission:staff-pick.edit')->middleware('permission:staff-pick.edit');
    Route::put('staff-pick/{process}/update', 'StaffPickController@update')->name('staff-pick.update')->middleware('permission:staff-pick.edit');
    Route::get('staff-pick/status/{process}/{status}', 'StaffPickController@status')->name('staff-pick.status')->middleware('permission:staff-pick.edit');
    Route::delete('staff-pick/{process}', 'StaffPickController@destroy')->name('staff-pick.destroy')->middleware('permission:staff-pick.destroy');

    // Staff Pick Candidates:
    Route::get('staff-pick-candidates/{process}/create', 'StaffPickCandidatesController@create')->name('staff-pick-candidates.create')->middleware('permission:staff-pick.edit');
    Route::post('staff-pick-candidates/store', 'StaffPickCandidatesController@store')->name('staff-pick-candidates.store')->middleware('permission:staff-pick.edit');
    Route::get('staff-pick-candidates/{candidate}/edit/{tab?}', 'StaffPickCandidatesController@edit')->name('staff-pick-candidates.edit')->middleware('permission:staff-pick.edit');
    Route::put('staff-pick-candidates/{candidate}/update', 'StaffPickCandidatesController@update')->name('staff-pick-candidates.update')->middleware('permission:staff-pick.edit');
    Route::get('staff-pick-candidates/{candidate}/destroy', 'StaffPickCandidatesController@destroy')->name('staff-pick-candidates.destroy')->middleware('permission:staff-pick.edit');
    Route::delete('staff-pick-candidates/{candidate}', 'StaffPickCandidatesController@destroy')->name('staff-pick-candidates.destroy')->middleware('permission:staff-pick.edit');

    // Staff Pick Observations:
    Route::post('staff-pick-observations/store', 'StaffPickObservationsController@store')->name('staff-pick-observations.store')->middleware('permission:staff-pick.edit');
    Route::get('staff-pick-observations/{observation}/destroy', 'StaffPickObservationsController@destroy')->name('staff-pick-observations.destroy')->middleware('permission:staff-pick.edit');

    // Stock Movements:
    Route::get('stock-movements', 'StockMovementsController@index')->name('stock-movements.index');
    Route::get('stock-movements/create', 'StockMovementsController@create')->name('stock-movements.create')->middleware('permission:stock-movements.create');
    Route::post('stock-movements/store', 'StockMovementsController@store')->name('stock-movements.store')->middleware('permission:stock-movements.create');
    Route::get('stock-movements/{movement}/edit', 'StockMovementsController@edit')->name('stock-movements.edit')->middleware('permission:stock-movements.edit');
    Route::put('stock-movements/{movement}/update', 'StockMovementsController@update')->name('stock-movements.update')->middleware('permission:stock-movements.edit');
    Route::delete('stock-movements/{movement}', 'StockMovementsController@destroy')->name('stock-movements.destroy')->middleware('permission:stock-movements.destroy');
    Route::get('stock-movements/status/{id}/{status}', 'StockMovementsController@status')->name('stock-movements.status')->middleware('permission:stock-movements.edit');

    // Stocks:
    Route::get('stocks/{product_id?}', 'StockController@index')->name('stocks.index')->where('product_id', '[0-9]+');
    Route::get('stocks/{product}/show', 'StockController@show')->name('stocks.show');
    Route::get('stocks/{product_id}/stores-stock', 'StockController@storesStock')->name('stocks.stores-stock');
    Route::post('stocks.store', 'StockController@store')->name('stocks.store')->middleware('permission:stocks.create');

    // Stores:
    Route::get('stores/index/{storeSelected?}', 'StoreController@index')->name('stores.index');
    Route::get('stores/create', 'StoreController@create')->name('stores.create')->middleware('permission:stores.create');
    Route::post('stores/store', 'StoreController@store')->name('stores.store')->middleware('permission:stores.create');
    Route::get('stores/{store}/edit', 'StoreController@edit')->name('stores.edit')->middleware('permission:stores.edit');
    Route::put('stores/{store}', 'StoreController@update')->name('stores.update')->middleware('permission:stores.edit');
    Route::delete('stores/{store}', 'StoreController@destroy')->name('stores.destroy')->middleware('permission:stores.destroy');
    Route::get('stores/status/{id}/{status}', 'StoreController@status')->name('stores.status')->middleware('permission:stores.edit');

    // Subtasks:
    Route::post('subtasks.store', 'SubtaskController@store')->name('subtasks.store')->middleware('permission:projects.edit');
    Route::get('subtasks/status/{subtask}/{status}', 'SubtaskController@status')->name('subtasks.status')->middleware('permission:projects.edit');
    Route::delete('subtasks/{subtask}', 'SubtaskController@destroy')->name('subtasks.destroy')->middleware('permission:projects.edit');

    // Task Messages:
    Route::post('task-messages/{task}/store', 'TaskMessagesController@store')->name('task-messages.store');
    Route::get('task-messages/{message}/destroy', 'TaskMessagesController@destroy')->name('task-messages.destroy');

    // Task Status:
    Route::post('task-status/store', 'TaskStatusController@store')->name('task-status.store');
    Route::get('task-status/{project}/delete/{status}', 'TaskStatusController@delete')->name('task-status.delete');
    Route::post('task-status/order-columns', 'TaskStatusController@orderColumns')->name('task-status.order-columns');

    // Tasks:
    Route::get('tasks', 'TaskController@index')->name('tasks.index');
    Route::post('tasks/store', 'TaskController@store')->name('tasks.store')->middleware('permission:projects.edit');
    Route::get('tasks/{task}/edit/{tab?}', 'TaskController@edit')->name('tasks.edit')->middleware('permission:projects.edit');
    Route::put('tasks/{task}/update', 'TaskController@update')->name('tasks.update')->middleware('permission:projects.edit');
    Route::get('tasks/{task}/status/{status}', 'TaskController@status')->name('tasks.status');
    Route::get('tasks/{task}/destroy', 'TaskController@destroy')->name('tasks.destroy')->middleware('permission:projects.edit');
    Route::post('tasks/order-column', 'TaskController@orderColumn')->name('tasks.order-column');
    Route::get('tasks/{task}/working-time', 'TaskController@workingTime')->name('tasks.working-time');
    Route::post('tasks.store-working-time', 'TaskController@storeWorkingTime')->name('tasks.store-working-time');
    Route::get('tasks/{task}/delete-working-time/{index}', 'TaskController@deleteWorkingTime')->name('tasks.delete-working-time');
    Route::get('tasks/{user}/member-tasks/{project}', 'TaskController@memberTasks')->name('tasks.member-tasks');

    // Team Members:
    Route::get('team-members', 'TeamMembersController@index')->name('team-members.index')->middleware('permission:teams.index');
    Route::get('team-members/{team}/create', 'TeamMembersController@create')->name('team-members.create')->middleware('permission:teams.edit');
    Route::post('team-members/store', 'TeamMembersController@store')->name('team-members.store')->middleware('permission:teams.edit');
    Route::get('team-members/{member}/edit', 'TeamMembersController@edit')->name('team-members.edit')->middleware('permission:teams.edit');
    Route::delete('team-members/{member}', 'TeamMembersController@destroy')->name('team-members.destroy')->middleware('permission:teams.edit');
    Route::get('team-members/{member}/set-leader', 'TeamMembersController@setLeader')->name('team-members.set-leader')->middleware('permission:teams.edit');
    Route::put('team-members/{member}/update', 'TeamMembersController@update')->name('team-members.update')->middleware('permission:teams.edit');

    // Teams:
    Route::get('teams', 'TeamController@index')->name('teams.index')->middleware('permission:teams.index');
    Route::get('teams/create', 'TeamController@create')->name('teams.create')->middleware('permission:teams.create');
    Route::post('teams/store', 'TeamController@store')->name('teams.store')->middleware('permission:teams.create');
    Route::get('teams/{team}/edit', 'TeamController@edit')->name('teams.edit')->middleware('permission:teams.edit');
    Route::put('teams/{team}/update', 'TeamController@update')->name('teams.update')->middleware('permission:teams.edit');
    Route::delete('teams/{team}', 'TeamController@destroy')->name('teams.destroy')->middleware('permission:teams.destroy');
    Route::get('teams/{team}/delete-logo', 'TeamController@deleteLogo')->name('teams.delete-logo')->middleware('permission:teams.edit');
    Route::get('teams/status/{id}/{status}', 'TeamController@status')->name('teams.status')->middleware('permission:teams.edit');

    // Timetable Items:
    Route::get('timetable-items', 'TimetableItemsController@index')->name('timetable-items.index');
    Route::get('timetable-items/create', 'TimetableItemsController@create')->name('timetable-items.create');
    Route::post('timetable-items/store', 'TimetableItemsController@store')->name('timetable-items.store');
    Route::get('timetable-items/{item}/edit', 'TimetableItemsController@edit')->name('timetable-items.edit');
    Route::put('timetable-items/{item}/update', 'TimetableItemsController@update')->name('timetable-items.update');
    Route::delete('timetable-items/{item}', 'TimetableItemsController@destroy')->name('timetable-items.destroy');
    Route::post('timetable-items/export', 'TimetableItemsController@export')->name('timetable-items.export');
    Route::post('timetable-items/store-rule', 'TimetableItemsController@storeRule')->name('timetable-items.store-rule');
    Route::get('timetable-items/status/{id}/{status}', 'TimetableItemsController@status')->name('timetable-items.status')->middleware('permission:timetable-items.edit');

    // Timetable Signed:
    Route::get('timetable-signed/list/{year?}', 'TimetableSignedController@index')->name('timetable-signed.index');
    Route::post('timetable-signed', 'TimetableSignedController@getTimetablesBy')->name('timetable-signed.list');
    Route::get('timetable-signed/{user}/show', 'TimetableSignedController@show')->name('timetable-signed.show');
    Route::get('timetable-signed/{year}/{month}/validate', 'TimetableSignedController@validateTimetable')->name('timetable-signed.validate');
    Route::get('timetable-signed/{ts}/sign', 'TimetableSignedController@sign')->name('timetable-signed.sign');
    Route::get('timetable-signed/{ts}/download', 'TimetableSignedController@download')->name('timetable-signed.download');

    // Timetables:
    Route::get('timetable/{item}/store', 'TimetableController@store')->name('timetable.store');
    Route::get('timetable/{timetable}/edit-hour', 'TimetableController@edit')->name('user-timetable.edit-hour');
    Route::post('timetable/update-hour', 'TimetableController@update')->name('timetable.update-hour');
    Route::get('employees-timetable', 'TimetableController@index')->name('employees-timetable.index');
    Route::post('employees-timetable', 'TimetableController@index')->name('employees-timetable.index');
    Route::post('timetable/add-hour', 'TimetableController@add')->name('timetable.add-hour')->middleware('permission:employees-timetable.edit');
    Route::delete('timetable/{timetable}', 'TimetableController@destroy')->name('timetable.destroy')->middleware('permission:employees-timetable.destroy');
    Route::post('timetable/revision', 'TimetableController@revisionHorarioOn')->name('timetable.revision-horario');
    Route::get('timetable/revision/{id}', 'TimetableController@revisionHorarioOff')->name('timetable.revision-horario');
    Route::get('timetable/authorize-concept/{timetable}', 'TimetableController@authorizeConcept')->name('timetable.authorize-concept');

    // Towns:
    Route::get('towns/{province}', 'TownController@index')->name('towns.index');
    Route::post('towns/store', 'TownController@store')->name('towns.store')->middleware('permission:countries.edit');
    Route::get('towns/status/{id}/{status}', 'TownController@status')->name('towns.status')->middleware('permission:countries.edit');
    Route::post('towns/update', 'TownController@update')->name('towns.update')->middleware('permission:countries.edit');
    Route::delete('towns/{town}', 'TownController@destroy')->name('towns.destroy')->middleware('permission:countries.edit');

    // Units:
    Route::get('units', 'UnitController@index')->name('units.index')->middleware('permission:units.index');
    Route::get('units/create', 'UnitController@create')->name('units.create')->middleware('permission:units.create');
    Route::post('units/store', 'UnitController@store')->name('units.store')->middleware('permission:units.create');
    Route::get('units/{unit}/edit', 'UnitController@edit')->name('units.edit')->middleware('permission:units.edit');
    Route::put('units/{unit}', 'UnitController@update')->name('units.update')->middleware('permission:units.edit');
    Route::get('units/status/{id}/{status}', 'UnitController@status')->name('units.status')->middleware('permission:units.edit');
    Route::delete('units/{unit}', 'UnitController@destroy')->name('units.destroy')->middleware('permission:units.destroy');

    // User Business Areas:
    Route::post('user-business-areas/store', 'UserBusinessAreaController@store')->name('user-business-areas.store');
    Route::get('user-business-areas/{business_area}/delete', 'UserBusinessAreaController@destroy')->name('user-business-areas.delete');

    // User Companies:
    Route::get('user-companies/{user}', 'UserCompaniesController@index')->name('user-companies.index')->middleware('permission:users.edit');
    Route::get('user-companies/status/{company_id}/{user_id}/{status}', 'UserCompaniesController@status')->name('user-companies.status');
    Route::post('user-companies/transfer', 'UserCompaniesController@transferCompany')->name('user-companies.transfer')->middleware('permission:companies.edit');
    Route::get('user-companies/{company_id}/show', 'UserCompaniesController@companyUsers')->name('user-companies.show');

    // User Cost Centers:
    Route::post('user-cost-centers/store', 'UserCostCentersController@store')->name('user-cost-centers.store');
    Route::get('user-cost-centers/{cost_center}/delete', 'UserCostCentersController@destroy')->name('user-cost-centers.delete');

    // User Docs:
    Route::get('user-docs/{doc}/destroy/{return?}', 'UserDocsController@destroy')->name('user-docs.destroy');

    // User Recommendations:
    Route::get('user-recommendations', 'UserRecommendationController@index')->name('user-recommendations.index');
    Route::get('user-recommendations/by-user', 'UserRecommendationController@byUser')->name('user-recommendations.by-user');
    Route::get('user-recommendations/create', 'UserRecommendationController@create')->name('user-recommendations.create');
    Route::post('user-recommendations/store', 'UserRecommendationController@store')->name('user-recommendations.store');
    Route::get('user-recommendations/{recommendation}/edit', 'UserRecommendationController@edit')->name('user-recommendations.edit');
    Route::put('user-recommendations/{recommendation}/update', 'UserRecommendationController@update')->name('user-recommendations.update');
    Route::get('user-recommendations/{recommendation}/deleteImage', 'UserRecommendationController@deleteImage')->name('user-recommendations.delete-image');
    Route::get('user-recommendations/{recommendation}/show', 'UserRecommendationController@show')->name('user-recommendations.show');
    Route::get('user-recommendations/companies', 'UserRecommendationController@companies')->name('user-recommendations.companies');
    Route::get('user-recommendations/set-company/{cs}/{status}', 'UserRecommendationController@setCompany')->name('user-recommendations.set-company');
    Route::get('user-recommendations/ranking/{tab?}', 'UserRecommendationController@ranking')->name('user-recommendations.ranking');

    // User Recommendation Votes:
    Route::post('user-recommendation-votes/store', 'UserRecommendationVoteController@store')->name('user-recommendation-votes.store');

    // User Timetables:
    Route::get('user-timetables/{user_id}/show/{employee}', 'UserTimetablesController@show')->name('user-timetables.show');
    Route::post('user-timetables/store', 'UserTimetablesController@store')->name('user-timetables.store');
    //Route::post('user-timetable/update-hour', 'UserTimetablesController@update')->name('user-timetable.update-hour');

    // User Workplaces:
    Route::post('user-workplaces/store', 'UserWorkplacesController@store')->name('user-workplaces.store');
    Route::get('user-workplaces/{workplace}/delete', 'UserWorkplacesController@destroy')->name('user-workplaces.delete');

    // Users:
    Route::resource('users', 'UserController', ['except' => ['store', 'edit']]);
    Route::post('users/store', 'UserController@store')->name('users.store');
    Route::get('users/{user}/edit/{tab?}', 'UserController@edit')->name('users.edit');
    Route::get('users/{user}/show', 'UserController@show')->name('users.show')->middleware('permission:users.show');
    Route::post('users/{user}/update_pwd', 'UserController@updatePwd')->name('users.update_pwd');
    Route::get('users/status/{id}/{status}', 'UserController@status')->name('users.status')->middleware('permission:users.edit');
    Route::get('users/delete-signature/{signature}', 'UserController@deleteSignature')->name('users.delete-signature');
    Route::post('users/{user}/store-image', 'UserController@storeImage')->name('users.store-image');
    Route::get('users/delete-image/{image}/{return?}', 'UserController@deleteImage')->name('users.delete-image');
    Route::get('users/set-featured/{image}', 'UserController@setFeatured')->name('users.set-featured');
    Route::get('users/set-public/{image}', 'UserController@setPublic')->name('users.set-public');
    Route::get('users/{user?}/timetables/{add_register?}', 'UserController@userTimetables')->name('users.timetables');
    Route::post('users/filter-timetables', 'UserController@userTimetables')->name('users.filter-timetables');
    Route::get('users/{user}/from-candidate', 'UserController@fromCandidate')->name('users.from-candidate');

    // Users All:
    Route::get('users-all', 'UserController@usersAll')->name('users-all.index')->middleware('permission:users-all.index');

    // Vehicle Images:
    Route::post('vehicle-images/{vehicle}/store', 'VehicleImagesController@store')->name('vehicle-images.store')->middleware('permission:vehicles.edit');
    Route::get('vehicle-images/{vehicle}/destroy/{image}', 'VehicleImagesController@destroy')->name('vehicle-images.destroy')->middleware('permission:vehicles.edit');
    Route::get('vehicle-images/set-featured/{vehicle}/{image}', 'VehicleImagesController@setFeatured')->name('vehicle-images.set-featured')->middleware('permission:vehicles.edit');
    Route::get('vehicle-images/set-thumb/{vehicle}/{image}', 'VehicleImagesController@setThumb')->name('vehicle-images.set-thumb')->middleware('permission:vehicles.edit');

    // Vehicles:
    Route::get('vehicles', 'VehicleController@index')->name('vehicles.index');
    Route::get('vehicles/create', 'VehicleController@create')->name('vehicles.create')->middleware('permission:vehicles.create');
    Route::post('vehicles/store', 'VehicleController@store')->name('vehicles.store')->middleware('permission:vehicles.create');
    Route::get('vehicles/{vehicle}/edit/{tab?}', 'VehicleController@edit')->name('vehicles.edit')->middleware('permission:vehicles.edit');
    Route::put('vehicles/{vehicle}/update', 'VehicleController@update')->name('vehicles.update')->middleware('permission:vehicles.edit');
    Route::get('vehicles/status/{vehicle}/{status}', 'VehicleController@status')->name('vehicles.status')->middleware('permission:vehicles.edit');
    Route::delete('vehicles/{vehicle}', 'VehicleController@destroy')->name('vehicles.destroy')->middleware('permission:vehicles.destroy');

    // Work Calendar:
    Route::get('work-calendar/{workplace_id?}/{year?}', 'WorkCalendarController@index')->name('work-calendar.index');
    Route::post('work-calendar', 'WorkCalendarController@calendarBy')->name('work-calendar.calendar-by');
    Route::post('work-calendar/store-intervals', 'WorkCalendarController@storeIntervals')->name('work-calendar.store-intervals');
    Route::post('work-calendar/copy', 'WorkCalendarController@copy')->name('work-calendar.copy');
    Route::post('work-calendar/set-ranks', 'WorkCalendarController@setRanks')->name('work-calendar.set-ranks');

    // Work Order Components:
    Route::post('work-order-components/store', 'WorkOrderComponentController@store')->name('work-order-components.store')->middleware('permission:work-orders.edit');
    Route::post('work-order-components/update', 'WorkOrderComponentController@update')->name('work-order-components.update')->middleware('permission:work-orders.edit');
    Route::delete('work-order-components/{component}', 'WorkOrderComponentController@destroy')->name('work-order-components.destroy')->middleware('permission:work-orders.edit');

    // Work Order Patterns:
    Route::get('work-order-patterns', 'WorkOrderPatternsController@index')->name('work-order-patterns.index');
    Route::get('work-order-patterns/create', 'WorkOrderPatternsController@create')->name('work-order-patterns.create');
    Route::post('work-order-patterns/store', 'WorkOrderPatternsController@store')->name('work-order-patterns.store');
    Route::get('work-order-patterns/{pattern}/edit', 'WorkOrderPatternsController@edit')->name('work-order-patterns.edit');
    Route::put('work-order-patterns/{pattern}/update', 'WorkOrderPatternsController@update')->name('work-order-patterns.update');
    Route::delete('work-order-patterns/{pattern}', 'WorkOrderPatternsController@destroy')->name('work-order-patterns.destroy');
    Route::get('work-order-patterns/status/{id}/{status}', 'WorkOrderPatternsController@status')->name('work-order-patterns.status');

    // Work Order Products:
    Route::post('work-order-products/store', 'WorkOrderProductsController@store')->name('work-order-products.store')->middleware('permission:work-orders.edit');
    Route::post('work-order-products/update', 'WorkOrderProductsController@update')->name('work-order-products.update')->middleware('permission:work-orders.edit');
    Route::delete('work-order-products/{product}', 'WorkOrderProductsController@destroy')->name('work-order-products.destroy')->middleware('permission:work-orders.edit');
    Route::get('work-order-products/{product}/status/{status}', 'WorkOrderProductsController@status')->name('work-order-products.status');

    // Work Orders:
    Route::get('work-orders', 'WorkOrderController@index')->name('work-orders.index')->middleware('permission:work-orders.index');
    Route::get('work-orders/create', 'WorkOrderController@create')->name('work-orders.create')->middleware('permission:work-orders.create');
    Route::get('work-orders/{work_order}/edit/{tab?}', 'WorkOrderController@edit')->name('work-orders.edit')->middleware('permission:work-orders.edit');
    Route::put('work-orders/{work_order}/update', 'WorkOrderController@update')->name('work-orders.update')->middleware('permission:work-orders.edit');
    Route::delete('work-orders/{work_order}', 'WorkOrderController@destroy')->name('work-orders.destroy')->middleware('permission:work-orders.destroy');
    Route::get('work-orders/{work_order}/status/{status}', 'WorkOrderController@status')->name('work-orders.status');

    // Workplaces:
    Route::get('workplaces/list/{company_id?}/{business_relationship?}', 'WorkplaceController@index')->name('workplaces.index')->where('company_id', '[0-9]+');
    Route::get('workplaces/{company_id?}/{business_relationship?}/create', 'WorkplaceController@create')->name('workplaces.create')->middleware('permission:workplaces.create');
    Route::post('workplaces/store', 'WorkplaceController@store')->name('workplaces.store')->middleware('permission:workplaces.create');
    Route::get('workplaces/{id}/edit/{business_relationship?}', 'WorkplaceController@edit')->name('workplaces.edit')->middleware('permission:workplaces.edit');
    Route::put('workplaces/{workplace}/update', 'WorkplaceController@update')->name('workplaces.update')->middleware('permission:workplaces.edit');
    Route::delete('workplaces/{workplace}', 'WorkplaceController@destroy')->name('workplaces.destroy')->middleware('permission:workplaces.destroy');
    Route::get('workplaces/status/{id}/{status}', 'WorkplaceController@status')->name('workplaces.status')->middleware('permission:workplaces.edit');
    Route::get('workplaces/delete-logo/{logo}/{business_relationship?}', 'WorkplaceController@deleteLogo')->name('workplaces.delete-logo')->middleware('permission:companies.edit');
    Route::get('workplaces/{workplace}/by-default', 'WorkplaceController@byDefault')->name('workplaces.by-default')->middleware('permission:workplaces.edit');
    Route::get('workplaces/{workplace}/{business_relationship}/featured', 'WorkplaceController@featured')->name('workplaces.featured')->middleware('permission:workplaces.edit');

    // Work User Calendar:
    Route::get('work-user-calendar/{user}/{year?}', 'WorkUserCalendarController@index')->name('work-user-calendar.index')->where('year', '[0-9]+');
    Route::post('work-user-calendar', 'WorkUserCalendarController@calendarBy')->name('work-user-calendar.calendar-by');
    Route::get('work-user-calendar-notify/{user}/notify-days', 'WorkUserCalendarController@notify')->name('work-user-calendar-notify.notify-days');
    Route::post('work-user-calendar/validate-all', 'WorkUserCalendarController@validateAll')->name('work-user-calendar.validate-all');
    Route::post('work-user-calendar/add-doc', 'WorkUserCalendarController@addDoc')->name('work-user-calendar.add-doc');
    Route::get('work-user-calendar/{doc}/delete-doc', 'WorkUserCalendarController@deleteDoc')->name('work-user-calendar.delete-doc');

    // JOKE:
    Route::get('close-joke', 'UserController@closeJoke')->name('close-joke');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Import Old DB:
//Route::get('/admin/import_db/{activate?}', 'Admin\ImportOldDBController@index')->name('import_db.index');

// Ajuste horarios:
//Route::get('/admin/adjust_timetables', 'Admin\TimetableController@adjustTimetables')->name('timetables.adjust_timetables');

//Route::get('/admin/adjust_timetables_workers', 'Admin\TimetableController@adjustTimetablesWorkers')->name('timetables.adjust_timetables_workers');

//Route::get('/admin/delete-weekends', 'Admin\TimetableController@deleteWeekends')->name('timetables.delete-weekends');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
