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
use App\Http\Controllers\Client\BookingExtensionController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\BookingServiceController;
use App\Http\Controllers\Client\ExtraServiceController;
use App\Http\Controllers\Client\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');;

Route::middleware(['auth:api'])->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::post('/booking', [BookingController::class, 'handleBooking']);
    Route::prefix('bookings')->group(function () {
        
        Route::get('/history', [BookingController::class, 'bookingHistory'])
            ->name('booking.history'); 

        Route::middleware('can:view,booking')->group(function () {
            Route::get('{booking}/confirmation', [BookingController::class, 'getBookingConfirmationPage'])
                ->name('booking.getBookingConfirmationPage');
            Route::get('{booking}/select-service', [BookingController::class, 'selectService'])
                ->name('booking.selectService');
            Route::post('{booking}/confirm-service', [BookingController::class, 'postConfirmBookingService'])
                ->name('booking.confirmService');;
            Route::get('{booking}/check-cancel', [BookingController::class, 'checkCancelability']);
            Route::get('/{booking}/check-refund', [BookingController::class, 'checkRefund']);

            Route::get('/{booking}', [BookingController::class, 'show'])
                ->where('booking', '[0-9]+') 
                ->name('booking.show');
            Route::post('/{booking}/cancel', [BookingController::class, 'cancelBooking']);

            Route::get('/{booking}/booking-services', [BookingServiceController::class, 'index']);
            Route::get('/{booking}/booking-services/unpaid-total', [BookingServiceController::class, 'unpaidTotal']);
            // ->middleware('can:view,booking')

            Route::get('/{booking}/booking-services/can-pay', [BookingServiceController::class, 'canPayPostpaid']);
            Route::post('/{booking}/booking-services', [BookingServiceController::class, 'create']);

            Route::get('/{booking}/booking-extensions/can-book', [BookingExtensionController::class, 'checkCanBookingExtension']);
            Route::post('/{booking}/booking-extensions', [BookingExtensionController::class, 'create'])
                ->can('bookAndPayAdditionalBooking', 'booking');
            Route::get('/{booking}/booking-extensions/{bookingExtension}/can-pay', [BookingExtensionController::class, 'canPay']);

            Route::get('/{booking}/checkout', [PaymentController::class, 'handlePay'])
                ->name('checkout');

        });
    }); 
});

Route::get('/checkout/vn-pay-callback', [PaymentController::class, 'handleVnPayCallback']);

// Route::middleware(['isAdmin'])->group(function () {
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
// });

Route::get('/services/by-type', [ExtraServiceController::class, 'getServicesByType']);
Route::get('/services', [ExtraServiceController::class, 'getAll']);