<?php

use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\IngressController;
use App\Http\Controllers\NamespaceController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\PodController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::controller(AuthController::class)->group(function () {
    Route::get('/','index')->name("login");
    Route::post('/Login','login')->name("Auth.login");
});

Route::middleware('auth')->group(function () {
    Route::get('/Dashboard', function () {
        return view('dashboard.index');
    })->name('Dashboard');

    Route::controller(AuthController::class)->group(function () {
        Route::get('/Logout','logout')->name("Auth.logout");
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/User/me','editMe')->name("User.editMe")
        ->middleware('can:updateMe,App\Models\User');
        Route::put('/User/{user}/me','updateMe')->name("User.updateMe")
        ->middleware('can:updateMe,App\Models\User');
        Route::patch('/User/{user}/password','updatePassword')->name("User.updatePassword")->middleware('can:updateMe,App\Models\User');
    });


    Route::controller(UserController::class)->group(function () {
        Route::get('/Users','index')->name("Users.index")->middleware('can:view,App\Models\User');
        Route::get('/Users/create','create')->name("Users.create")->middleware('can:create,App\Models\User');
        Route::post('/Users','store')->name("Users.store")->middleware('can:store,App\Models\User');
        Route::get('/Users/{user}/edit','edit')->name("Users.edit")->middleware('can:edit,App\Models\User');
        Route::put('/Users/{user}','update')->name("Users.update")->middleware('can:update,App\Models\User');
        Route::delete('/Users/{user}','destroy')->name("Users.destroy")->middleware('can:delete,App\Models\User');
    })->middleware(AdminMiddleware::class);

    Route::controller(NodeController::class)->group(function () {
        Route::resource('/Nodes',NodeController::class)->only(['index','show']);
    });

    Route::controller(NamespaceController::class)->group(function () {
        Route::resource('/Namespaces',NamespaceController::class)->except(['edit','update']);
    });

    Route::controller(PodController::class)->group(function () {
        Route::resource('/Pods',PodController::class)->except(['edit','update','destroy']);
        Route::delete('/Pods/{Namespace}/{Pod}','destroy')->name("Pods.destroy");
    });

    Route::controller(DeploymentController::class)->group(function () {
        Route::resource('/Deployments',DeploymentController::class)->except(['edit','update']);
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::resource('/Services',ServiceController::class)->except(['edit','update']);
    });

    Route::controller(IngressController::class)->group(function () {
        Route::resource('/Ingresses',IngressController::class)->except(['edit','update']);
    });
});