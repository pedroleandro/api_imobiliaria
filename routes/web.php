<?php

use \Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'Web', 'as' => 'web.'], function () {

    /** Página inicial */
    Route::get('/', 'WebController@home')->name('home');

    /** Página destaque */
    Route::get('/destaque', 'WebController@spotlight')->name('spotlight');

    /** Página de contato */
    Route::get('/contato', 'WebController@contact')->name('contact');
    Route::post('/contato/sendEmail', 'WebController@sendEmail')->name('sendEmail');
    Route::post('/contato/sucesso', 'WebController@sendEmailSuccess')->name('sendEmailSuccess');

    /** Página de locação */
    Route::get('/quero-alugar', 'WebController@rent')->name('rent');
    Route::get('/quero-alugar/{slug}', 'WebController@rentProperty')->name('rentProperty');

    /** Página de compra */
    Route::get('/quero-comprar', 'WebController@sale')->name('sale');
    Route::get('/quero-comprar/{slug}', 'WebController@saleProperty')->name('saleProperty');

    /** Página de filtro */
    Route::match(['post', 'get'], '/filtro', 'WebController@filter')->name('filter');

    Route::get('/experiencias', 'WebController@experience')->name('experience');
    Route::get('/experiencias/{slug}', 'WebController@experienceCategory')->name('experienceCategory');

});

Route::group(['prefix' => 'component', 'namespace' => 'Web', 'as' => 'component.'], function () {

    Route::post('main-filter/search', 'FilterController@search')->name('main-filter.search');
    Route::post('main-filter/category', 'FilterController@category')->name('main-filter.category');
    Route::post('main-filter/type', 'FilterController@type')->name('main-filter.type');
    Route::post('main-filter/neighborhood', 'FilterController@neighborhood')->name('main-filter.neighborhood');
    Route::post('main-filter/bedrooms', 'FilterController@bedrooms')->name('main-filter.bedrooms');
    Route::post('main-filter/suites', 'FilterController@suites')->name('main-filter.suites');
    Route::post('main-filter/bathrooms', 'FilterController@bathrooms')->name('main-filter.bathrooms');
    Route::post('main-filter/garage', 'FilterController@garage')->name('main-filter.garage');
    Route::post('main-filter/price-base', 'FilterController@priceBase')->name('main-filter.priceBase');
    Route::post('main-filter/price-limit', 'FilterController@priceLimit')->name('main-filter.priceLimit');

});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function () {

    /** Formulário de Login */
    Route::get('/', 'AuthController@showLoginForm')->name('login');
    Route::post('/login', 'AuthController@login')->name('login.do');

    /** Logout */
    Route::get('/logout', 'AuthController@logout')->name('logout');

    /** Rotas protegidas */
    Route::group(['middleware' => ['auth']], function () {

        /** Dashboard Home */
        Route::get('/home', 'AuthController@home')->name('home');

        /** Usuários */
        Route::get('users/team', 'UserController@team')->name('users.team');
        Route::resource('users', 'UserController');

        /** Empresas */
        Route::resource('companies', 'CompanyController');

        /** Imóveis */
        Route::post('properties/image-set-cover', 'PropertyController@imageSetCover')->name('properties.imageSetCover');
        Route::delete('properties/image-remove', 'PropertyController@imageRemove')->name('properties.imageRemove');
        Route::resource('properties', 'PropertyController');

        /** Contratos */
        Route::post('contracts/get-data-owner', 'ContractController@getDataOwner')->name('contracts.getDataOwner');
        Route::post('contracts/get-data-acquirer', 'ContractController@getDataAcquirer')->name('contracts.getDataAcquirer');
        Route::post('contracts/get-data-property', 'ContractController@getDataProperty')->name('contracts.getDataProperty');
        Route::resource('contracts', 'ContractController');

    });
});
