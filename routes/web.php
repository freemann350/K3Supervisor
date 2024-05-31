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
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomResourceController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ResourceControlMiddleware;

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
        Route::get('/Namespaces','index')->name("Namespaces.index");
        Route::get('/Namespaces/New','create')->name("Namespaces.create")->middleware(ResourceControlMiddleware::class);
        Route::get('/Namespaces/{namespace}','show')->name("Namespaces.show");
        Route::post('/Namespaces/Store','store')->name("Namespaces.store")->middleware(ResourceControlMiddleware::class);
        Route::delete('/Namespaces/{namespace}','destroy')->name("Namespaces.destroy")->middleware(ResourceControlMiddleware::class);
    });

    Route::controller(PodController::class)->group(function () {
        Route::get('/Pods','index')->name("Pods.index");
        Route::get('/Pods/New','create')->name("Pods.create")->middleware(ResourceControlMiddleware::class);
        Route::get('/Pods/{Namespace}/{Pod}','show')->name("Pods.show");
        Route::post('/Pods/Store','store')->name("Pods.store")->middleware(ResourceControlMiddleware::class);
        Route::delete('/Pods/{Namespace}/{Pod}','destroy')->name("Pods.destroy")->middleware(ResourceControlMiddleware::class);
    });

    Route::controller(DeploymentController::class)->group(function () {
        Route::get('/Deployments','index')->name("Deployments.index");
        Route::get('/Deployments/New','create')->name("Deployments.create")->middleware(ResourceControlMiddleware::class);
        Route::get('/Deployments/{Namespace}/{Deployment}','show')->name("Deployments.show");
        Route::post('/Deployments/Store','store')->name("Deployments.store")->middleware(ResourceControlMiddleware::class);
        Route::delete('/Deployments/{Namespace}/{Deployment}','destroy')->name("Deployments.destroy")->middleware(ResourceControlMiddleware::class);
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::get('/Services','index')->name("Services.index");
        Route::get('/Services/New','create')->name("Services.create")->middleware(ResourceControlMiddleware::class);
        Route::get('/Services/{Namespace}/{Service}','show')->name("Services.show");
        Route::post('/Services/Store','store')->name("Services.store")->middleware(ResourceControlMiddleware::class);
        Route::delete('/Services/{Namespace}/{Service}','destroy')->name("Services.destroy")->middleware(ResourceControlMiddleware::class);
    });

    Route::controller(IngressController::class)->group(function () {
        Route::get('/Ingresses','index')->name("Ingresses.index");
        Route::get('/Ingresses/New','create')->name("Ingresses.create")->middleware(ResourceControlMiddleware::class);
        Route::get('/Ingresses/{Namespace}/{Ingress}','show')->name("Ingresses.show");
        Route::post('/Ingresses/Store','store')->name("Ingresses.store")->middleware(ResourceControlMiddleware::class);
        Route::delete('/Ingresses/{Namespace}/{Ingress}','destroy')->name("Ingresses.destroy")->middleware(ResourceControlMiddleware::class);
    });

    Route::controller(BackupController::class)->group(function () {
        Route::get('/Backups','index')->name("Backups.index")->middleware(ResourceControlMiddleware::class);
        Route::post('/Backups','store')->name("Backups.store")->middleware(ResourceControlMiddleware::class);
    });

    Route::controller(CustomResourceController::class)->group(function () {
        Route::get('/CustomResources','index')->name("CustomResources.index")->middleware(ResourceControlMiddleware::class);
        Route::post('/CustomResources','store')->name("CustomResources.store")->middleware(ResourceControlMiddleware::class);
    });
});