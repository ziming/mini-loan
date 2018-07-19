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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// User Loan Contracts
Route::get('/users/{id}/loan-contracts', 'API\UserLoanContractsController@index');
Route::post('/users/{id}/loan-contracts', 'API\UserLoanContractsController@store');


// Loan Contracts Generic
Route::get('/loan-contracts/{id}', 'API\LoanContractsController@show');
Route::match(['put', 'patch'], '/loan-contracts/{id}', 'API\LoanContractsController@update');
Route::delete('/loan-contracts/{id}', 'API\LoanContractsController@destroy');


// Payments
Route::post('/loan-contracts/{id}/payments', 'API\LoanContractPaymentsController@store');


// Loan Contracts Admin
Route::post('/loan-contracts/{id}/approve', 'API\ApprovedLoanContractsController@store');
