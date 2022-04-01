<?php

use DataGrade\LydiaPay\Http\Controllers\LydiaPayController;
use Illuminate\Support\Facades\Route;

// Has prefix
Route::any('/{driver}', [LydiaPayController::class, 'verify']);
