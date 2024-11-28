<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(["prefix" => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});


Route::middleware('auth:api')->group(function () {
    // Routes for Magazines
    Route::prefix('magazine')->group(function () {
        Route::get('/show', [MagazineController::class, 'showMagazines']); // show All Magazines
        Route::post('/store', [MagazineController::class, 'storeMagazine']);
        Route::put('/update/{magazine_id}', [MagazineController::class, 'updateMagazine']);
        Route::delete('/delete/{magazine_id}', [MagazineController::class, 'deleteMagazine']);
    });
    //Routes for Articles
    Route::prefix('article')->group(function () {
        Route::post('/store', [ArticleController::class, 'storeArticle']);
        Route::get('/show/{magazine_id}', [ArticleController::class, 'articlesByMagazine']);
        Route::get('/details/{article_id}', [ArticleController::class, 'articleDetails']);
        Route::put('update/{article_id}', [ArticleController::class, 'updateArticle']);
        Route::delete('delete/{article_id}', [ArticleController::class, 'deleteArticle']);
    });
    //Routes for subscription
    Route::prefix('subscription')->group(function () {
        Route::get('/filter', [SubscriptionController::class, 'filterSubscription']);
        Route::post('/create', [SubscriptionController::class, 'createSubscription']);
        Route::get('/showUserSub', [SubscriptionController::class, 'listUserSubscriptions']);
        Route::put('/update/{subscription_id}', [SubscriptionController::class, 'updateSubscription']);
        Route::delete('/delete/{subscription_id}', [SubscriptionController::class, 'deleteSubscription']);
    });
    //Routes for payments
    Route::prefix('payments')->group(function () {
        Route::post('/{subscription_id}', [PaymentController::class, 'createPayment']);
        Route::get('/show', [PaymentController::class, 'showUserPayments']);
        Route::get('/showAll', [PaymentController::class, 'showAllPayments']);
    });
    //Routes for comments
    Route::prefix('comment')->group(function () {
        Route::post('/add', [CommentController::class, 'addComment']);
        Route::put('update/{comment_id}', [CommentController::class, 'updateComment']);
        Route::delete('delete/{comment_id}', [CommentController::class, 'deleteComment']);
    });
    //Routes for manage users(Publisher , Subscriber ) by Admin
    Route::prefix('admin-manage')->group(function () {
        Route::post('/addAdmin', [ManageUserController::class, 'addAdmin']);
        Route::post('/addPublisher', [ManageUserController::class, 'addPublisher']);
        Route::get('/subscribers', [ManageUserController::class, 'listSubscriber']);
        Route::get('/publishers', [ManageUserController::class, 'listPublishers']);
        Route::delete('/subscriber/delete/{subscriber_id}', [ManageUserController::class, 'deleteSubscriber']);
        Route::delete('/publisher/delete/{publisher_id}', [ManageUserController::class, 'deletePublisher']);
        Route::put('/subscriber/updatePassword/{subscriber_id}', [ManageUserController::class, 'updateSubscriberPassword']);
        Route::put('/publisher/updatePassword/{publisher_id}', [ManageUserController::class, 'updatePublisherPassword']);
    });
});
//Routes for payments too
Route::get('payment/success/{subscriptionId}/{userId}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel/{subscriptionId}', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
