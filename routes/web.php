<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.index');
});

Route::get('/cool', function () {
    return view('dashboard.indexcool');
});

Route::get('/catalog', function(){
    return view('catalog.index');
});
