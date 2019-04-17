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

Route::get('/', function () {
    return view('geo.main');
})->middleware('verified');

Route::get('/about', function () {
    return view('geo.about');
})->middleware('verified');

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/welcome', 'HomeController@welcome')->name('welcome');

Route::group(['prefix' => 'places'], function () {

    Route::get('/search', 'Geo\PlaceController@search');

    Route::get('/', 'Geo\PlaceController@index')->name('places');

    Route::get('/add', 'Geo\PlaceController@create');

    Route::get('/{id}/edit', 'Geo\PlaceController@edit')->name('places.edit');

    Route::get('/{id}/delete', 'Geo\PlaceController@destroy')->name('places.delete');

    Route::post('/update', 'Geo\PlaceController@update')->name('places.update');

    Route::post('/store', 'Geo\PlaceController@store');
});

Route::group(['prefix' => 'directions'], function () {

    Route::get('/search', 'Geo\UserDirectionsController@search');

    Route::get('/', 'Geo\UserDirectionsController@index')->name('directions');

    Route::get('/add', 'Geo\UserDirectionsController@create');

    Route::get('/{id}/edit', 'Geo\UserDirectionsController@edit')->name('directions.edit');

    Route::get('/{id}/delete', 'Geo\UserDirectionsController@destroy')->name('directions.delete');

    Route::post('/update', 'Geo\UserDirectionsController@update')->name('directions.update');

    Route::post('/store', 'Geo\UserDirectionsController@store');
});




Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
