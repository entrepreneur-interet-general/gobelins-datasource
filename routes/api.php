<?php

use Illuminate\Http\Request;

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

Route::get('products', [
    'as' => 'products', 'uses' => 'ProductController@index'
]);

Route::get('products/{inventory}', [
    'as' => 'product', 'uses' => 'ProductController@show'
]);

Route::get('authors', [
    'as' => 'authors', 'uses' => 'AuthorController@index'
]);

Route::get('authors/{id}', [
    'as' => 'author', 'uses' => 'AuthorController@show'
]);
