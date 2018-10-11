<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    echo '<h1>Welcome to plass ott api</h1>';
});*/

Route::get('write','SimulatorController@write');
Route::get('read','SimulatorController@read');
Route::get('simulator','SimulatorController@index');
Route::post('simulator/get-json-request','SimulatorController@getJsonRequest');
Route::post('simulator/get-documentation-page','SimulatorController@getDocumentationPage');
Route::post('simulate','SimulatorController@simulate');


/*Route::post('countries','CountryController@index');
Route::post('divisions','DivisionController@index');
Route::post('districts','DistrictController@index');
Route::post('areas','AreaController@index');
Route::post('sub-areas','AreaController@subAreas');
Route::post('roads','RoadController@index');
Route::post('post-codes','PostCodeController@index');*/


Route::post('service-operators','ServiceOperatorController@index');
Route::post('service-operator-ids','ServiceOperatorController@index');
Route::post('device-types','DeviceTypeController@index');
Route::post('system-settings','SystemController@index');
Route::post('registration-subscriber','RegistrationController@index');
Route::post('re-registration','RegistrationController@index');
Route::post('confirm-code','RegistrationController@index');
Route::post('sign-in','RegistrationController@index');
Route::post('api-login','RegistrationController@index');
Route::post('subscriber-profile','SubscriberController@index');
Route::post('subscriber-profile-update','SubscriberController@index');
Route::post('heart-beat','SystemController@index');
Route::post('viewing-content','SystemController@index');
Route::post('contents','ProgramController@index');
Route::post('feature-contents','ProgramController@index');
Route::post('history-contents','ProgramController@index');
Route::post('relative-contents','ProgramController@index');
Route::post('relative-contents-ext','ProgramController@index');
Route::post('search-contents','ProgramController@index');
Route::post('popular-contents','ProgramController@index');
Route::post('purchase-content-by-wallet','PurchaseController@index');
Route::post('set-favorites','FavoriteController@index');
Route::post('favorite-contents','FavoriteController@index');
Route::post('packages','PackageController@index');
Route::post('purchase-package-by-wallet','PurchaseController@index');
Route::post('content-order-id','OrderController@index');
Route::post('package-details','PackageController@index');
Route::post('subscribed-packages','PackageController@index');
Route::post('categories','CategoryController@index');
Route::post('epgs','EpgController@index');
Route::post('new-epgs','EpgController@index');
Route::post('bkash-info','PaymentGatewayController@index');
Route::post('check-status','TestController@checkSession');
Route::post('set-fcm-token','NotificationController@index');
Route::post('newly-uploaded-contents','ProgramController@index');
Route::post('add-amount-by-scratch-card','PaymentController@index');
Route::post('forgot-password','RegistrationController@index');
Route::post('reset-password','RegistrationController@index');
Route::post('change-password','RegistrationController@index');
Route::post('about-us','SystemController@index');
Route::get('/','AuthController@index');
Route::post('authenticate','AuthController@authenticate');
Route::get('logout','AuthController@logout');


Route::get('test','TestController@index');

//Route::get('memcached-write','TestController@memcachedWrite');

/*** Documentation Related Route ****/

/*Route::get('doc','DocController@index');
Route::get('doc/api','DocController@apiHome');
Route::get('doc/process-flow','DocController@processFlow');
Route::get('doc/registration-subscriber','DocController@register');
Route::get('doc/confirm-code','DocController@confirm');
Route::get('doc/heart-beat','DocController@heartBeat');
Route::get('doc/viewing-channel','DocController@viewingChannel');
Route::get('doc/programs','DocController@programs');
Route::get('doc/packages','DocController@packages');
Route::get('doc/categories','DocController@categories');
Route::get('doc/sub-categories','DocController@subCategorise');*/




