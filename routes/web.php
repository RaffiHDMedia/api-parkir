<?php

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
    return view('welcome');
});
// routes/web.php

use App\Http\Controllers\PaymentController;

Route::post('/payment/create', [PaymentController::class, 'create']);
Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook']);

Route::get('/success', function () {
    return view('success'); // Pastikan file 'success.blade.php' ada di folder 'resources/views'
});

Route::get('/failure', function () {
    return view('failure'); // Pastikan file 'failure.blade.php' ada di folder 'resources/views'
});

