<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CustomerTypeController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomPricingController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Client\BookingController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/profile', [AuthController::class, 'profile']);

    Route::post('/booking/confirm-service/{booking_id}', [BookingController::class, 'postConfirmBookingService'])
        ->name('booking.confirmService');

    Route::get('/booking/booking-confirmation/{booking_id}', [BookingController::class, 'getBookingConfirmationPage'])
        ->name('booking.getBookingConfirmationPage');

    Route::get('/booking/booking-service/{booking_id}', [BookingController::class, 'selectService'])
        ->name('booking.selectService');

    Route::post('/booking', [BookingController::class, 'handleBooking'])
        ->name('booking.handleBooking');
    
});

Route::prefix('admin')->group(function () {
    Route::resource('customer-types', CustomerTypeController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('room-types', RoomTypeController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('room-pricings', RoomPricingController::class);
});