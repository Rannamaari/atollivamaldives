<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LiveaboardController;
use App\Http\Controllers\OperationsDocumentDownloadController;
use App\Http\Controllers\RequestQuoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/about-us', AboutController::class)->name('about');
Route::get('/faq', FaqController::class)->name('faq');
Route::get('/liveaboards', LiveaboardController::class)->name('liveaboards.index');
Route::get('/resorts', [AccommodationController::class, 'index'])->defaults('type', 'resort')->name('resorts.index');
Route::get('/guest-houses', [AccommodationController::class, 'index'])->defaults('type', 'guesthouse')->name('guesthouses.index');
Route::get('/city-hotels', [AccommodationController::class, 'index'])->defaults('type', 'city_hotel')->name('cityhotels.index');
Route::get('/packages', [AccommodationController::class, 'index'])->defaults('type', 'package')->name('packages.index');
Route::get('/travel-products', [AccommodationController::class, 'index'])->name('accommodations.index');
Route::get('/travel-products/{accommodation}', [AccommodationController::class, 'show'])->name('accommodations.show');
Route::redirect('/stays', '/travel-products', 301);
Route::get('/stays/{accommodation}', fn ($accommodation) => redirect()->route('accommodations.show', $accommodation, 301));
Route::redirect('/journal', '/blog', 301);
Route::redirect('/journal/{post}', '/blog/{post}', 301);
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/request-quote', RequestQuoteController::class)->name('request-quote');
Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:5,1')->name('inquiries.store');
Route::get('/admin/operations/documents/{document}/download', OperationsDocumentDownloadController::class)->name('operations.documents.download');
