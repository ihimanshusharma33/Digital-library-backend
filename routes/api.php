<?php

use App\Http\Controllers\courseContoller;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\questionPaperContoller;
use App\Http\Controllers\userController;
use App\Http\Controllers\booksContoller;
use App\Http\Controllers\EBooksController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
Route::get('/books', [booksContoller::class, 'getBooks']); 
Route::get('/books/availability', [booksContoller::class, 'checkAvailability']); 
Route::get('/resources', [ResourceController::class, 'getResources']);
Route::get('/statistics', [StatisticsController::class, 'getStatistics']);
Route::get('/course', [courseContoller::class, 'getCourse']); // Public course listing
Route::get('/course/{id}', [courseContoller::class, 'getCourseById']); // Public course by ID
Route::get('/notes', [NotesController::class, 'getNotes']); // Public notes listing
Route::get('/oldquestion', [questionPaperContoller::class, 'getOldQuestion']); // Public question papers
Route::get('/ebooks', [EBooksController::class, 'getEBooks']); // Public e-books catalog
Route::get('/notices', [NotificationController::class, 'index']); // Public notices

// Password reset routes (public)
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Routes for any authenticated user

Route::middleware('jwt.auth')->group(function () {
    // User profile and personal data
    Route::get('/user/{id}', [userController::class, 'getUserById'])->middleware('jwt.verify.self'); // Only see own or admin
    Route::get('libraryCard/{id}', [userController::class, 'getLibraryCard'])->middleware('jwt.verify.self');
    Route::get('/issued-books', [userController::class, 'getUserIssuedBooks']);
    Route::get('/issued-booksbyId/{id}', [UserController::class, 'getUserIssuedBooksbyID']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/profile/update', [userController::class, 'updateProfile']);
});

// Routes for staff and admin
Route::middleware('jwt.staff')->group(function () {
    // User management
    Route::post('/user', [userController::class, 'addUser']);
    Route::put('/user/{id}', [userController::class, 'updateUser']);
    Route::get('/user', [userController::class, 'getUser']); // All users listing
    Route::get('/search/users', [userController::class, 'searchUsers']);
    Route::get('/user/search/library-id', [userController::class, 'searchUserByLibraryId']);
    // Book lending operations
    Route::post('/issue-book', [userController::class, 'issueBook']);
    Route::post('/return-book', [booksContoller::class, 'returnBook']);
    
    // Book management
    Route::post('/books', [booksContoller::class, 'addBooks']);
    Route::put('/books/{id}', [booksContoller::class, 'updateBooks']);
    
    // Course management
    Route::post('/course', [courseContoller::class, 'addCourse']);
    Route::put('/course/{id}', [courseContoller::class, 'updateCourse']);
    
    // Notes management
    Route::post('/notes', [NotesController::class, 'addNotes']);
    Route::put('/notes/{id}', [NotesController::class, 'updateNotes']);
    
    // Question papers management
    Route::post('/oldquestion', [questionPaperContoller::class, 'addOldQuestion']);
    Route::put('/oldquestion/{id}', [questionPaperContoller::class, 'updateQuestionPaper']);
    
    // E-Books management
    Route::post('/ebooks', [EBooksController::class, 'addEBook']);
    Route::put('/ebooks/{id}', [EBooksController::class, 'updateEBook']);
    
    // Notifications management
    Route::prefix('notices')->group(function () {
        Route::post('/', [NotificationController::class, 'store']);
        Route::put('/{id}', [NotificationController::class, 'update']);
    });

    Route::delete('/user/{id}', [userController::class, 'deleteUser']);
    Route::delete('/books/{id}', [booksContoller::class, 'deleteBooks']);
    Route::delete('/course/{id}', [courseContoller::class, 'deleteCourse']);
    Route::delete('/notes/{id}', [NotesController::class, 'deleteNotes']);
    Route::delete('/oldquestion/{id}', [questionPaperContoller::class, 'deleteQuestionPaper']);
    Route::delete('/ebooks/{id}', [EBooksController::class, 'deleteEBook']);
    Route::delete('/notices/{id}', [NotificationController::class, 'destroy']);
});
