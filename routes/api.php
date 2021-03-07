<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('login', 'UserController@login');

Route::any('listUser', 'UserController@listUser');

Route::any('createUser', 'UserController@createUser');

Route::any('updateUser', 'UserController@updateUser');

Route::any('deleteUser', 'UserController@deleteUser');

Route::any('dashboard', 'ServiceController@dashboard');

Route::any('getCategoriesByService', 'ServiceController@getCategoriesByService');

/*Route::group(['middleware' => ['auth:api']], function () {
    Route::any('listCategory', 'CategoryController@listCategory');
});*/

//Route::any('listCategory', 'ServiceController@listCategory');
Route::any('listCategory', 'CategoryController@listCategory');

//Route::any('createCategory', 'ServiceController@createCategory');
Route::any('createCategory', 'CategoryController@createCategory');

//Route::any('deleteCategory', 'ServiceController@deleteCategory');
Route::any('deleteCategory', 'CategoryController@deleteCategory');

Route::any('createProduct', 'ProductController@createProduct');

Route::any('listProduct', 'ProductController@listProduct');

Route::any('deleteProduct', 'ProductController@deleteProduct');







