<?php

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

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('notice', 'Backend\AdminController@notice')->name('main.notice');

#Admin Routes
Route::get('admin/login', 'Backend\AuthController@redirectToGoogle')->name('login');
Route::get('admin/logout', 'Backend\AuthController@logout')->name('logout');
Route::get('admin/callback', 'Backend\AuthController@handleGoogleCallback')->name('callback');

Route::group(['middleware' => 'acl'], function() {

    Route::get('admin', 'Backend\HomeController@index')->name('main.index');
    Route::get('admin/clearOldOffer', 'Backend\HomeController@clearOldOffer')->name('home.clearOldOffer');
    Route::get('admin/correctLead', 'Backend\HomeController@correctLead')->name('home.correctLead');
    Route::get('admin/clearFinishLog', 'Backend\HomeController@clearFinishLog')->name('home.clearFinishLog');

    Route::get('users.dataTables', ['uses' => 'Backend\UsersController@dataTables', 'as' => 'users.dataTables']);
    Route::resource('admin/users', 'Backend\UsersController');

    Route::get('offers.dataTables', ['uses' => 'Backend\OffersController@dataTables', 'as' => 'offers.dataTables']);
    Route::get('offers.test/{id}', ['uses' => 'Backend\OffersController@test', 'as' => 'offers.test']);
    Route::get('offers.reject/{id}', ['uses' => 'Backend\OffersController@reject', 'as' => 'offers.reject']);
    Route::get('offers.accept/{id}', ['uses' => 'Backend\OffersController@accept', 'as' => 'offers.accept']);
    Route::get('offers.clear/{id}', ['uses' => 'Backend\OffersController@clear', 'as' => 'offers.clear']);
    Route::resource('admin/offers', 'Backend\OffersController');

    Route::get('groups.dataTables', ['uses' => 'Backend\GroupsController@dataTables', 'as' => 'groups.dataTables']);
    Route::resource('admin/groups', 'Backend\GroupsController');

    Route::get('network_clicks.dataTables', ['uses' => 'Backend\NetworkClicksController@dataTables', 'as' => 'network_clicks.dataTables']);
    Route::get('network_clicks/export-to-excel', 'Backend\NetworkClicksController@export')->name('network_clicks.export');
    Route::resource('admin/network_clicks', 'Backend\NetworkClicksController');


    Route::get('networks.dataTables', ['uses' => 'Backend\NetworksController@dataTables', 'as' => 'networks.dataTables']);
    Route::get('networks.cron/{id}', ['uses' => 'Backend\NetworksController@cron', 'as' => 'networks.cron']);
    Route::resource('admin/networks', 'Backend\NetworksController');

});



#Frontend Routes
Route::get('/', 'Frontend\MainController@index')->name('frontend.index');
Route::get('camp', 'Frontend\MainController@camp')->name('frontend.camp');
Route::get('check', 'Frontend\MainController@check')->name('frontend.check');
Route::get('postback', 'Frontend\MainController@inside')->name('frontend.inside.postback');
Route::get('hashpostback', 'Frontend\MainController@inside')->name('frontend.inside.hashpostback');
Route::post('postback', 'Frontend\MainController@inside')->name('frontend.inside.postback2');

// Login-as-user
Route::post('visudo/login-as-user', 'ViSudoController@loginAsUser')
    ->name('visudo.login_as_user');

Route::post('visudo/return', 'ViSudoController@return')
    ->name('visudo.return');
