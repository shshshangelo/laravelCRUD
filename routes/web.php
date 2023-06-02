<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('employee.employee_template');
});

Route::get('employee',[ EmployeeController::class, 'ShowPage']);
Route::post('employee/create_data', [ EmployeeController:: class, 'createEmpInfoData']);
Route::get('employee/getDataById/{emp_id}', [ EmployeeController:: class, 'getDataById']);
Route::get('employee/getEmpData', [ EmployeeController:: class, 'getEmpData']);
Route::post('employee/update_data', [ EmployeeController:: class, 'updateEmpInfoData']);
Route::post('employee/removeDataById',[ EmployeeController:: class, 'removeEmpInfoById']);
