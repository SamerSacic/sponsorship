<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SponsorableSponsorshipsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/test', function () {
    return response()->json(['message' => 'Test endpoint hit!'], 201);
});
Route::view('/mockup', 'sponsorable-sponsorships.new');

Route::get('/{sponsorableSlug}/sponsorships/new', [SponsorableSponsorshipsController::class, 'new']);
Route::post('/{sponsorableSlug}/sponsorships', [SponsorableSponsorshipsController::class, 'store']);
