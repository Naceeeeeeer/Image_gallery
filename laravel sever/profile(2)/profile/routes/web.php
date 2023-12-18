<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NatureController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\AnimalController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/image/upload', [ImageController::class, 'showUploadForm'])->name('image.upload');
    Route::post('/image/upload', [ImageController::class, 'uploadImage'])->name('image.upload.post');
    Route::get('/images', [ImageController::class, 'showImages'])->name('images');
    Route::get('/search_types', [ImageController::class, 'gotosearch'])->name('gotosearch');
    Route::delete('/image/delete/{id}', [ImageController::class, 'deleteImage'])->name('image.delete');
    Route::get('/images/edit/{file_name}', [ImageController::class, 'editImage'])->name('image.edit');
    Route::put('/images/{file_name}/histogramme', [ImageController::class, 'getHistogram'])->name('image.update');
    Route::put('/images/{file_name}/pallette', [ImageController::class, 'getMoments'])->name('image.update1');
    Route::put('/images/{file_name}/Moment', [ImageController::class, 'getPallette'])->name('image.update2');  
    Route::get('/showHistogram', [ImageController::class, 'showHistogram'])->name('showHistogram');
    Route::get('/showPallette', [ImageController::class, 'showPallette'])->name('showPallette');
    Route::get('/showMoment', [ImageController::class, 'showMoment'])->name('showMoment');
    Route::post('/Search/Simple/{id}', [ImageController::class, 'search'])->name('search.image');
    Route::post('/Search_RF/{id}', [ImageController::class, 'search_RF'])->name('search_RF.image');
    Route::post('/Search_by_RF/{id}', [ImageController::class, 'search_by_RF'])->name('search_by_RF.image');
    Route::post('/enregistrer-image', [ImageController::class,'enregistrerImage'])->name('enregistrer-image');

    Route::get('/nature', 'App\Http\Controllers\NatureController@index')->name('nature');
    Route::post('/nature/uploadnature', [NatureController::class, 'uploadImage'])->name('nature.upload.post');
    Route::delete('/nature/delete/{id}', [NatureController::class, 'deleteImage'])->name('nature.delete');
    Route::get('/nature/edit/{file_name}', [NatureController::class, 'editImage'])->name('nature.edit');


    Route::get('/voiture', 'App\Http\Controllers\VoitureController@index')->name('voiture');
    Route::post('/voiture/uploadnature', [VoitureController::class, 'uploadImage'])->name('voiture.upload.post');
    Route::delete('/voiture/delete/{id}', [VoitureController::class, 'deleteImage'])->name('voiture.delete');
    Route::get('/voiture/edit/{file_name}', [VoitureController::class, 'editImage'])->name('voiture.edit');


    Route::get('/animal', 'App\Http\Controllers\AnimalController@index')->name('animal');
    Route::post('/animal/uploadnature', [AnimalController::class, 'uploadImage'])->name('animal.upload.post');
    Route::delete('/animal/delete/{id}', [AnimalController::class, 'deleteImage'])->name('animal.delete');
    Route::get('/animal/edit/{file_name}', [AnimalController::class, 'editImage'])->name('animal.edit');
    Route::put('/animal/{file_name}/pallette', [AnimalController::class, 'getMoments'])->name('animal.update1');


});

require __DIR__.'/auth.php';
