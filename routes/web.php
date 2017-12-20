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

Route::get('/', function () {
    return view('welcome');
});

#Admin Routes
Route::get('admin/login', 'Backend\AuthController@redirectToGoogle')->name('auth.login');
Route::get('admin/logout', 'Backend\AuthController@logout')->name('logout');
Route::get('admin/callback', 'Backend\AuthController@handleGoogleCallback')->name('auth.callback');


Route::get('admin', 'Backend\HomeController@index')->name('main.index');
Route::get('admin/control', 'Backend\HomeController@control')->name('home.control');
Route::get('admin/cron', 'Backend\HomeController@cron')->name('home.cron');
Route::get('admin/thongke', 'Backend\HomeController@thongke')->name('home.thongke');
Route::get('admin/clearlead', 'Backend\HomeController@clearlead')->name('home.clearlead');
Route::get('admin/statistic/{content}', 'Backend\HomeController@statistic')->name('home.statistic');
Route::get('admin/ajax/{content}', 'Backend\HomeController@ajax')->name('home.ajax');
Route::get('admin/recent-lead', 'Backend\HomeController@ajaxSiteRecentLead')->name('home.ajaxSiteRecentLead');
Route::get('admin/offertest/{id}', 'Backend\HomeController@submit')->name('home.submit');

Route::get('users.dataTables', ['uses' => 'Backend\UsersController@dataTables', 'as' => 'users.dataTables']);
Route::resource('admin/users', 'Backend\UsersController');

Route::get('offers.dataTables', ['uses' => 'Backend\OffersController@dataTables', 'as' => 'offers.dataTables']);
Route::resource('admin/offers', 'Backend\OffersController');

Route::get('groups.dataTables', ['uses' => 'Backend\GroupsController@dataTables', 'as' => 'groups.dataTables']);
Route::resource('admin/groups', 'Backend\GroupsController');


Route::get('networks.dataTables', ['uses' => 'Backend\NetworksController@dataTables', 'as' => 'networks.dataTables']);
Route::resource('admin/networks', 'Backend\NetworksController');

#Frontend Routes
Route::get('/', 'Frontend\MainController@index')->name('frontend.index');
Route::get('camp', 'Frontend\MainController@camp')->name('frontend.camp');
Route::get('check', 'Frontend\MainController@check')->name('frontend.check');
Route::get('postback', 'Frontend\MainController@inside')->name('frontend.inside.postback');
Route::get('hashpostback', 'Frontend\MainController@inside')->name('frontend.inside.hashpostback');
Route::post('postback', 'Frontend\MainController@inside')->name('frontend.inside.postback2');