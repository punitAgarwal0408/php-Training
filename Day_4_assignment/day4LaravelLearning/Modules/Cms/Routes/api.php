<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\CommentController;

Route::prefix('cms')->group(function () {
    // List comments for a page
    Route::get('pages/{id}/comments', [CommentController::class, 'index']);
    // Add comment to a page
    Route::post('pages/{id}/comments', [CommentController::class, 'store'])
        ->middleware('cms.spam');
    // Update comment
    Route::put('comments/{id}', [CommentController::class, 'update']);
    // Delete comment
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);
    // Approve comment (admin)
    Route::post('comments/{id}/approve', [CommentController::class, 'approve']);
    // Reject comment (admin)
    Route::post('comments/{id}/reject', [CommentController::class, 'reject']);
});


