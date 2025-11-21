<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Controllers\TrainingController;


Route::post('/training-times', function () {
    
    return ['status' => 'ok'];
});