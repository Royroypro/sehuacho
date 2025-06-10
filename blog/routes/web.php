<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "Hello, World!";
});


Route::get('/post/{post}', function ($post) {
    return "aque se mostra el post {$post}";
});



Route::get('/post/{post}/{category?}', function ($post, $category = null) {
    if($category) {
        return "Post {$post} de la categoria {$category}";
    }
    return "Post {$post}";
});

