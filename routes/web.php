<?php

use App\Source\Messaging\App\Controllers\ConversationController;
use App\Source\Messaging\App\Controllers\MessageController;
use App\Source\Public\App\Controllers\PublicController;
use App\Source\Ride\App\Controllers\RideController;
use App\Source\RideRequest\App\Controllers\RideRequestController;
use App\Source\User\App\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// AUTH
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('ride')->group(function () {
        Route::get('create', [RideController::class, 'showCreate'])->name('ride.create');
        Route::post('save', [RideController::class, 'save'])->name('ride.save');
        Route::get('my', [RideController::class, 'myRides'])->name('ride.my-rides');
        Route::post('delete/{id}', [RideController::class, 'delete'])->name('ride.delete');
    });

    Route::prefix('ride-request')->group(function () {
        Route::get('my-requests/{rideId}', [RideRequestController::class, 'myRequests'])->name(
            'ride-request.my-requests'
        );
        Route::post('pending/{rideId}', [RideRequestController::class, 'sendRequest'])->name(
            'ride-request.request-ride'
        );
        Route::post('cancel', [RideRequestController::class, 'cancelRequest'])->name(
            'ride-request.cancel'
        );
        Route::post('accept-reject', [RideRequestController::class, 'acceptOrReject'])->name(
            'ride-request.accept-reject'
        );
    });

    Route::prefix('user')->group(function () {
        Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
    });

    Route::prefix('messaging')->group(function () {
        Route::prefix('conversation')->group(function () {
            Route::get('/list', [ConversationController::class, 'list'])->name('messaging.conversation.list');
            Route::get('/create/{userId}', [ConversationController::class, 'createForm'])->name(
                'messaging.conversation.create-form'
            );
            Route::post('/create-conversation', [ConversationController::class, 'create'])->name(
                'messaging.conversation.create'
            );
        });

        Route::prefix('message')->group(function () {
            Route::get('/single/{id}', [MessageController::class, 'list'])->name('messaging.message.single');
            Route::get('/send', [MessageController::class, 'send'])->name('messaging.message.send');
        });
    });
});

//PUBLIC
Route::prefix('ride')->group(function () {
    Route::get('search', [RideController::class, 'search'])->name('ride.search');
});

Route::get('/', [RideController::class, 'search']);
Route::get('contact', [PublicController::class, 'contact'])->name('contact');
Route::post('send-message', [PublicController::class, 'sendMessage'])->name('contact.send-message');

//Route::get('/auth/redirect/{driver}', [SocialController::class, 'redirect'])->name('social_login.redirect');
//Route::get('/auth/callback/{driver}', [SocialController::class, 'callback'])->name('social_login.callback');

