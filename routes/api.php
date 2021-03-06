<?php

use App\Http\Controllers\AuthController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('send-mail', function () {
   
    $details = [
        'title' => 'Bạn hãy chọn vào link bên dưới để xác nhận',
        'body' => 'This is for testing email using smtp'
    ];
   
    \Mail::to('quangnd.hn@havaz.vn')->send(new \App\Mail\MailConfirmRegister($details));
   
    dd("Email is Sent.");
});

