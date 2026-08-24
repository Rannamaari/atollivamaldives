<?php
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LiveaboardController;
use Illuminate\Support\Facades\Route;

Route::get('/',HomeController::class)->name('home');
Route::get('/faq',FaqController::class)->name('faq');
Route::get('/liveaboards',LiveaboardController::class)->name('liveaboards.index');
Route::get('/stays',[AccommodationController::class,'index'])->name('accommodations.index');
Route::get('/stays/{accommodation}',[AccommodationController::class,'show'])->name('accommodations.show');
Route::redirect('/journal', '/blog', 301);
Route::redirect('/journal/{post}', '/blog/{post}', 301);
Route::get('/blog',[BlogController::class,'index'])->name('blog.index');
Route::get('/blog/{post}',[BlogController::class,'show'])->name('blog.show');
Route::post('/inquiries',[InquiryController::class,'store'])->middleware('throttle:5,1')->name('inquiries.store');
