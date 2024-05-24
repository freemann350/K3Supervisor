<?php

use App\Http\Controllers\ClusterController;
use App\Http\Controllers\DashboardController;
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

Route::get('/testing', function () {
    return view('welcome');
});


Route::controller(AuthController::class)->group(function () {
    Route::get('/','index')->name("login");
    Route::post('/Login','login')->name("Auth.login");
});

Route::middleware('auth')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/Dashboard','index')->name("Dashboard");
    });

    Route::controller(AuthController::class)->group(function () {
        Route::get('/Logout','logout')->name("Auth.logout");
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/User/me','editMe')->name("Users.editMe")
        ->middleware('can:updateMe,App\Models\User');
        Route::put('/User/{user}/me','updateMe')->name("Users.updateMe")
        ->middleware('can:updateMe,App\Models\User');
        Route::patch('/User/{user}/password','updatePassword')->name("Users.updatePassword")->middleware('can:updateMe,App\Models\User');
    });


    Route::controller(UserController::class)->group(function () {
        Route::get('/Users','index')->name("Users.index")->middleware('can:view,App\Models\User');
        Route::get('/Users/create','create')->name("Users.create")->middleware('can:create,App\Models\User');
        Route::post('/Users','store')->name("Users.store")->middleware('can:store,App\Models\User');
        Route::get('/Users/{user}/edit','edit')->name("Users.edit")->middleware('can:edit,App\Models\User');
        Route::put('/Users/{user}','update')->name("Users.update")->middleware('can:update,App\Models\User');
        Route::delete('/Users/{user}','destroy')->name("Users.destroy")->middleware('can:delete,App\Models\User');
    })->middleware(AdminMiddleware::class);

    Route::controller(ClusterController::class)->group(function () {
        Route::resource('/Clusters',ClusterController::class);
        Route::get('/Clusters/selectCluster/{device}','selectCluster')->name('Clusters.selectCluster');
    });

    Route::controller(NodeController::class)->group(function () {
        Route::resource('/Nodes',NodeController::class)->only(['index','show']);
    });

    Route::controller(NamespaceController::class)->group(function () {
        Route::resource('/Namespaces',NamespaceController::class)->except(['edit','update']);
    });

    Route::controller(PodController::class)->group(function () {
        Route::resource('/Pods',PodController::class)->except(['edit','update','destroy','show']);
        Route::get('/Pods/{Namespace}/{Pod}','show')->name("Pods.show");
        Route::delete('/Pods/{Namespace}/{Pod}','destroy')->name("Pods.destroy");
    });

    Route::controller(DeploymentController::class)->group(function () {
        Route::resource('/Deployments',DeploymentController::class)->except(['edit','update','destroy','show']);
        Route::get('/Deployments/{Namespace}/{Deployment}','show')->name("Deployments.show");
        Route::delete('/Deployments/{Namespace}/{Deployment}','destroy')->name("Deployments.destroy");
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::resource('/Services',ServiceController::class)->except(['edit','update','destroy','show']);
        Route::get('/Services/{Namespace}/{Service}','show')->name("Services.show");
        Route::delete('/Services/{Namespace}/{Service}','destroy')->name("Services.destroy");
    });

    Route::controller(IngressController::class)->group(function () {
        Route::resource('/Ingresses',IngressController::class)->except(['edit','update','destroy','show']);
        Route::get('/Ingresses/{Namespace}/{Ingress}','show')->name("Ingresses.show");
        Route::delete('/Ingresses/{Namespace}/{Ingress}','destroy')->name("Ingresses.destroy");
    });
});