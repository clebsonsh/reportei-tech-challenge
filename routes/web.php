<?php

use App\Http\Controllers\RepositoryController;
use Illuminate\Support\Facades\Route;

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
    return redirect()->route('repositories.index');
});

Route::middleware('auth')->group(function () {
    Route::get('repositories', [RepositoryController::class, 'index'])
        ->name('repositories.index');

    Route::get('repositories/{repository}', [RepositoryController::class, 'show'])
        ->name('repositories.show');
});


require __DIR__ . '/auth.php';
