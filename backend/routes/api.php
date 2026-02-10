<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PracownikController;
use App\Http\Controllers\PrzelozonyController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/activate/{token}', [AuthController::class, 'activate']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:administrator')->prefix('admin')->group(function () {
        Route::get('/uzytkownicy', [AdminController::class, 'getUzytkownicy']);
        Route::get('/uzytkownicy/{id}', [AdminController::class, 'getUzytkownik']);
        Route::post('/uzytkownicy', [AdminController::class, 'dodajUzytkownika']);
        Route::put('/uzytkownicy/{id}', [AdminController::class, 'edytujUzytkownika']);
        Route::delete('/uzytkownicy/{id}', [AdminController::class, 'usunUzytkownika']);
        Route::get('/raporty', [AdminController::class, 'getRaporty']);
        Route::get('/eksport-csv', [AdminController::class, 'eksportujCSV']);
        Route::get('/role', [AdminController::class, 'getRole']);
    }
    );

    Route::middleware('role:przelozony')->prefix('przelozony')->group(function () {
        Route::post('/godziny', [PrzelozonyController::class, 'dodajGodziny']);
        Route::get('/zespol', [PrzelozonyController::class, 'getZespol']);
        Route::get('/godziny/{userId}', [PrzelozonyController::class, 'getGodzinyPracownika']);
        Route::post('/komentarze', [PrzelozonyController::class, 'dodajKomentarz']);
        Route::get('/statystyki', [PrzelozonyController::class, 'getStatystyki']);
    }
    );

    Route::middleware('role:pracownik')->prefix('pracownik')->group(function () {
        Route::get('/godziny', [PracownikController::class, 'getMojeGodziny']);
        Route::get('/godziny/{rok}/{miesiac}', [PracownikController::class, 'getGodzinyMiesiac']);
        Route::get('/podsumowanie', [PracownikController::class, 'getPodsumowanie']);
        Route::post('/komentarze', [PracownikController::class, 'dodajKomentarz']);
    }
    );
});
