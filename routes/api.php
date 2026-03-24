<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\ArticleController; 
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\AbonneController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\MediaController;


Route::get('/test', function () {
    return response()->json(['message' => 'API fonctionne']);
});
// Routes publiques pour l'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Newsletter (Public)
Route::post('/newsletter/subscribe', [AbonneController::class, 'store']);
// Contact (Public)
Route::post('/contacts', [App\Http\Controllers\ContactController::class, 'store']);
// Redirection propre de l'API en cas de non-authentification
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');
// Routes publiques (lecture seule)
Route::get('articles/published', [ArticleController::class, 'published']);
Route::apiResource('articles', ArticleController::class)->only(['show']);
Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('biographies', BiographyController::class)->only(['index', 'show']);
Route::apiResource('documents', DocumentController::class)->only(['index', 'show']);
Route::get('documents/{document}/download', [DocumentController::class, 'download']);
Route::apiResource('media', MediaController::class)->only(['index', 'show']);
// Routes protégées nécessitant un token d'authentification
Route::middleware('auth:sanctum')->group(function () {
    // Utilisateur courant & Deconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Routes protégées (création, modification, suppression)
    Route::apiResource('articles', ArticleController::class)->except(['show']);
    Route::put('articles/{article}/publish', [ArticleController::class, 'publish']); 
    Route::put('articles/{article}/unpublish', [ArticleController::class, 'unpublish']);
    Route::apiResource('projects', ProjectController::class)->except(['index', 'show']);
    Route::apiResource('biographies', BiographyController::class)->except(['index', 'show']);
    // Gestion de la newsletter
    Route::apiResource('abonnes', AbonneController::class)->only(['index', 'destroy']);
    // Gestion des messages de contact
    Route::apiResource('contacts', App\Http\Controllers\ContactController::class)->except(['store', 'update']);
    // Gestion documentaire
    Route::apiResource('documents', DocumentController::class)->except(['index', 'show']);
    // Gestion des médias
    Route::apiResource('media', MediaController::class)->except(['index', 'show']);
    // Archives et Brouillons de Newsletters
    Route::apiResource('newsletters', NewsletterController::class);
    Route::post('/newsletters/{id}/send', [NewsletterController::class, 'sendNewsletter']); 
});
