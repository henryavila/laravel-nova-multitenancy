<?php

use HenryAvila\LaravelNovaMultitenancy\Http\Controllers\SelectTenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('select-tenant')
	->middleware('web', 'auth')
	->group(function () {
		Route::get('/', [SelectTenantController::class, 'index'])->name('select-tenant');
		Route::post('/', [SelectTenantController::class, 'store'])->name('select-tenant-store');
	});
