<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimpleApi;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/total_cr_type', [SimpleApi::class, 'getTotalCrType']);
Route::get('/total_cr_chart', [SimpleApi::class, 'getTotalCrChart']);
Route::get('/total_cr_top', [SimpleApi::class, 'getTopWidget']);
Route::get('/acr_list', [SimpleApi::class, 'getAcrList']);
Route::post('/create_acr', [SimpleApi::class, 'postCreateAcr']);