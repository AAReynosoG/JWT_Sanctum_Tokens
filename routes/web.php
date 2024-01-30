<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudController;

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
    return view('crud.register');
});

Route::post('/register', [CrudController::class, 'store'])->name('store');
Route::get('/index', [CrudController::class, 'index'])->name('index');
Route::put('/update/{userId}', [CrudController::class, 'update'])->name('update')->where('userId', '[0-9]+');
Route::delete('/delete/{userId}', [CrudController::class, 'delete'])->name('delete')->where('userId', '[0-9]+');



Route::get('/form/register', function () {
    return view('crud.register');
})->name('store.view');

Route::get('/form/index', function () {
    return view('crud.index');
})->name('index.view');

Route::get('/form/update/{userId}', [CrudController::class, 'updateView'])
    ->name('update.view')->where('userId', '[0-9]+');

Route::get('/form/delete/{userId}', [CrudController::class, 'deleteView'])
    ->name('delete.view')->where('userId', '[0-9]+');

