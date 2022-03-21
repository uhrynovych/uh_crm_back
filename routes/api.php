<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ===============
// auth routes
// ===============
Route::group(['prefix' => 'auth'], function () {
    // Route::middleware('auth:api')->get('/user', function (Request $request) {
    //     return $request->user();
    // });

    Route::group(['namespace' => 'Api'], function () {
        Route::group(['namespace' => 'Auth'], function () {
            Route::post('register', 'RegisterController');
            Route::post('login', 'LoginController');
            Route::get('logout', 'LogoutController')->middleware('auth:api');
        });
    });
});

// ===============
// For authorized
// ===============
Route::group(['middleware' => 'auth:api'], function() {
    Route::group(['namespace' => 'Api'], function () {

        // ==============================
        // Magazine routes
        // ==============================
        Route::group(['prefix' => 'magazine'], function () {
            Route::get('list', 'MagazineController@list');
            Route::get('{id}', 'MagazineController@list')->where('id', '[0-9]+');
            Route::post('{id}', 'MagazineController@update')->where('id', '[0-9]+');
            Route::post('create', 'MagazineController@add');
            Route::post('remove/{id}', 'MagazineController@remove');
        });

        // =============================================
        // Warehouse group
        // =============================================
        Route::group(['namespace' => 'Warehouse'], function () {
            // ==============================
            // Contractor routes
            // ==============================
            Route::group(['prefix' => 'contractor'], function () {
                Route::get('list', 'СontractorController@list');
                Route::get('{id}', 'СontractorController@list')->where('id', '[0-9]+');
                Route::post('{id}', 'СontractorController@update')->where('id', '[0-9]+');
                Route::post('create', 'СontractorController@add');
                Route::post('remove/{id}', 'СontractorController@remove');

                // ==============================
                // Contractor Collaborators routes
                // ==============================
                Route::get('{contractor_id}/list', 'CollaboratorsContractorController@list')->where('contractor_id', '[0-9]+');
                Route::get('{contractor_id}/{id}', 'CollaboratorsContractorController@list')->where('id', '[0-9]+')->where('contractor_id', '[0-9]+');
                Route::post('{contractor_id}/{id}', 'CollaboratorsContractorController@update')->where('id', '[0-9]+')->where('contractor_id', '[0-9]+');
                Route::post('{contractor_id}/create', 'CollaboratorsContractorController@add')->where('contractor_id', '[0-9]+');
                Route::post('{contractor_id}/remove/{id}', 'CollaboratorsContractorController@remove')->where('id', '[0-9]+')->where('contractor_id', '[0-9]+');
            });
        });
    });
});
