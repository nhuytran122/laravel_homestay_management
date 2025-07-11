<?php

namespace App\Providers;

use App\Repositories\Booking\BookingRepositoryInterface;
use App\Repositories\Booking\EloquentBookingRepository;
use App\Repositories\BookingExtension\BookingExtensionRepositoryInterface;
use App\Repositories\BookingExtension\EloquentBookingExtensionRepository;
use App\Repositories\BookingPricingSnapshot\BookingPricingSnapshotRepositoryInterface;
use App\Repositories\BookingPricingSnapshot\EloquentBookingPricingSnapshotRepository;
use App\Repositories\BookingService\BookingServiceRepositoryInterface;
use App\Repositories\BookingService\EloquentBookingServiceRepository;
use App\Repositories\Branch\BranchRepositoryInterface;
use App\Repositories\Branch\EloquentBranchRepository;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Customer\EloquentCustomerRepository;
use App\Repositories\CustomerType\CustomerTypeRepositoryInterface;
use App\Repositories\CustomerType\EloquentCustomerTypeRepository;
use App\Repositories\Employee\EloquentEmployeeRepository;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\Payment\EloquentPaymentRepository;
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\PaymentDetail\EloquentPaymentDetailRepository;
use App\Repositories\PaymentDetail\PaymentDetailRepositoryInterface;
use App\Repositories\Refund\EloquentRefundRepository;
use App\Repositories\Refund\RefundRepositoryInterface;
use App\Repositories\Role\EloquentRoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Room\EloquentRoomRepository;
use App\Repositories\Room\RoomRepositoryInterface;
use App\Repositories\RoomPricing\EloquentRoomPricingRepository;
use App\Repositories\RoomPricing\RoomPricingRepositoryInterface;
use App\Repositories\RoomStatusHistory\EloquentRoomStatusHistoryRepository;
use App\Repositories\RoomStatusHistory\RoomStatusHistoryRepositoryInterface;
use App\Repositories\RoomType\EloquentRoomTypeRepository;
use App\Repositories\RoomType\RoomTypeRepositoryInterface;
use App\Repositories\Service\EloquentServiceRepository;
use App\Repositories\Service\ServiceRepositoryInterface;
use App\Repositories\User\EloquentUserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CustomerTypeRepositoryInterface::class, EloquentCustomerTypeRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, EloquentRoleRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EloquentEmployeeRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, EloquentBranchRepository::class);
        $this->app->bind(RoomRepositoryInterface::class, EloquentRoomRepository::class);
        $this->app->bind(RoomTypeRepositoryInterface::class, EloquentRoomTypeRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, EloquentServiceRepository::class);
        $this->app->bind(RoomPricingRepositoryInterface::class, EloquentRoomPricingRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, EloquentBookingRepository::class);   
        $this->app->bind(BookingServiceRepositoryInterface::class, EloquentBookingServiceRepository::class);   
        $this->app->bind(BookingExtensionRepositoryInterface::class, EloquentBookingExtensionRepository::class);    
        $this->app->bind(BookingPricingSnapshotRepositoryInterface::class, EloquentBookingPricingSnapshotRepository::class);
        $this->app->bind(RoomStatusHistoryRepositoryInterface::class, EloquentRoomStatusHistoryRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, EloquentPaymentRepository::class);
        $this->app->bind(PaymentDetailRepositoryInterface::class, EloquentPaymentDetailRepository::class);
        $this->app->bind(RefundRepositoryInterface::class, EloquentRefundRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}