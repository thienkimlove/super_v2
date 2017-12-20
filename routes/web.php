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
Route::get('admin/login', 'Backend\AuthController@redirectToGoogle');
Route::get('admin/logout', 'Backend\AuthController@logout');
Route::get('admin/callback', 'Backend\AuthController@handleGoogleCallback');


Route::get('admin', 'Backend\HomeController@index');
Route::get('admin/control', 'Backend\HomeController@control');
Route::get('admin/cron', 'Backend\HomeController@cron');
Route::get('admin/thongke', 'Backend\HomeController@thongke');
Route::get('admin/clearlead', 'Backend\HomeController@clearlead');
Route::get('admin/statistic/{content}', 'Backend\HomeController@statistic');
Route::get('admin/ajax/{content}', 'Backend\HomeController@ajax');
Route::get('admin/recent-lead', 'Backend\HomeController@ajaxSiteRecentLead');
Route::get('admin/offertest/{id}', 'Backend\HomeController@submit');
Route::resource('admin/users', 'Backend\UsersController');
Route::resource('admin/offers', 'Backend\OffersController');
Route::resource('admin/groups', 'Backend\GroupsController');
Route::resource('admin/networks', 'Backend\NetworksController');

#Frontend Routes
Route::get('/', 'Frontend\MainController@index');
Route::get('camp', 'Frontend\MainController@camp');
Route::get('check', 'Frontend\MainController@check');
Route::get('postback', 'Frontend\MainController@inside');
Route::get('hashpostback', 'Frontend\MainController@inside');
Route::post('postback', 'Frontend\MainController@inside');