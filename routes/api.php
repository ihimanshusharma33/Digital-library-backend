<?php

use App\Http\Controllers\courseContoller;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\questionPaperContoller;
use App\Http\Controllers\userController;
use App\Http\Controllers\booksContoller;
use App\Http\Controllers\EBooksController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup',[AuthController::class,'signup'])->name('signup');
// Route::post('/logout', [AuthController::class, 'logout']);
// Route::post('/refresh', [AuthController::class, 'refresh']);

// Resources API - parameterized route to fetch books, notes, and question papers
Route::get('/resources', [ResourceController::class, 'getResources']);

// course Api
Route::post('/course',[courseContoller::class,'addCourse']);
Route::get('/course',[courseContoller::class,'getCourse']);
Route::put('/course/{id}',[courseContoller::class,'updateCourse']);
Route::delete('/course/{id}',[courseContoller::class,'deleteCourse']);

// User Api
Route::post('/user',[userController::class,'addUser']);
Route::get('/user',[userController::class,'getUser']);
// Route::put('/user/{id}',[userController::class,'updateUser']);
// Route::delete('/user/{id}',[userController::class,'deleteUser']);
// Route::get('libraryCard/{id}',[userController::class,'getLibraryCard']);
// Route::get('issuebooks',[userController::class,'getLibraryCard']);

//  books Api
Route::post('/books',[booksContoller::class,'addBooks']);
Route::get('/books',[booksContoller::class,'getBooks']);
Route::put('/books/{id}',[booksContoller::class,'updateBooks']);
Route::delete('/books/{id}',[booksContoller::class,'deleteBooks']);

// notes Api
Route::post('/notes',[NotesController::class,'addNotes']);
Route::get('/notes',[NotesController::class,'getNotes']);
Route::put('/notes/{id}',[NotesController::class,'updateNotes']);
Route::delete('/notes/{id}',[NotesController::class,'deleteNotes']);

// old question Paper Api
Route::post('/oldquestion',[questionPaperContoller::class,'addOldQuestion']);
Route::get('/oldquestion',[questionPaperContoller::class,'getOldQuestion']);
Route::put('/oldquestion/{id}',[questionPaperContoller::class,'updateQuestionPaper']);
Route::delete('/oldquestion/{id}',[questionPaperContoller::class,'deleteQuestionPaper']);

// e-Books Api
Route::post('/ebooks',[EBooksController::class,'addEBook']);
Route::get('/ebooks',[EBooksController::class,'getEBooks']);
Route::put('/ebooks/{id}',[EBooksController::class,'updateEBook']);
Route::delete('/ebooks/{id}',[EBooksController::class,'deleteEBook']);

// Notifications Api
Route::prefix('notices')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/', [NotificationController::class, 'store']);
    Route::get('/user', [NotificationController::class, 'getUserNotifications']);
    Route::get('/{id}', [NotificationController::class, 'show']);
    Route::put('/{id}', [NotificationController::class, 'update']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});