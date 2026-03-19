<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\ArticleController; 
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\AbonneController;

Route::get('/test', function () {
    return response()->json(['message' => 'API fonctionne']);
});

// Routes publiques pour l'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Newsletter (Public)
Route::post('/newsletter/subscribe', [AbonneController::class, 'store']);

// Redirection propre de l'API en cas de non-authentification
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

// Routes publiques (lecture seule)
Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('biographies', BiographyController::class)->only(['index', 'show']);

// Routes protégées nécessitant un token d'authentification
Route::middleware('auth:sanctum')->group(function () {
    
    // Utilisateur courant & Deconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Routes protégées (création, modification, suppression)
    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
    Route::apiResource('projects', ProjectController::class)->except(['index', 'show']);
    Route::apiResource('biographies', BiographyController::class)->except(['index', 'show']);

    // Gestion de la newsletter
    Route::apiResource('abonnes', AbonneController::class)->only(['index', 'destroy']);
});
