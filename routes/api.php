<?php

use App\Http\Controllers\AuthContoller;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PretController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    try {
        $user = $request->user();

        if (! $user) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Utilisateur non connecté',
                ], 404)
            );
        }

        return response()->json([
            'message' => 'Utilisateur connecté',
            'user' => $user,
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }
})->middleware('auth:sanctum');

// Auth routes
Route::post('/login', [AuthContoller::class, 'login']);
Route::post('/logout', [AuthContoller::class, 'logout']);

// Email verification routes
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
    ->middleware('auth:sanctum');

// User routes
Route::get('/users', [UserController::class, 'index']);
Route::get('/user/{user}', [UserController::class, 'show']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/update-password', [AuthContoller::class, 'updatePassword']);
Route::post('/user/{user}', [UserController::class, 'update']);
Route::delete('/user/{user}', [UserController::class, 'destroy']);

// Client routes
Route::resource('clients', ClientController::class)->except(['create', 'edit']);

// Pret routes
Route::resource('prets', PretController::class)->except(['create', 'edit']);
Route::get('/prets-client/{client}', [PretController::class, 'index']);

// Paiement routes
Route::resource('paiements', PaiementController::class)->except(['create', 'edit']);
Route::get('/paiements-pret/{pret}', [PaiementController::class, 'getByPret']);

Route::post('/send-sms', [SmsController::class, 'sendMessage']);
