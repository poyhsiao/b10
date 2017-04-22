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
    return view('welcome');
})->name('index');


Route::group([
    'prefix' => 'v1',
], function () {
    Route::get('img/{filename}', 'ImageController@display')
        ->name('imageDisplay')
        ->middleware('ImageExt', 'ImageSize');
});

Route::group([
    'prefix' => 'view'
], function () {
    Route::get('upload', 'ImageController@view')
        ->name('viewView');

    Route::post('file', 'ImageController@uploadFile')
        ->name('viewUpload');
});