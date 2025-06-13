<?php

namespace App\Providers;

use App\Repositories\Branch\BranchRepositoryInterface;
use App\Repositories\Branch\EloquentBranchRepository;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Customer\EloquentCustomerRepository;
use App\Repositories\CustomerType\CustomerTypeRepositoryInterface;
use App\Repositories\CustomerType\EloquentCustomerTypeRepository;
use App\Repositories\Employee\EloquentEmployeeRepository;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\Role\EloquentRoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Room\EloquentRoomRepository;
use App\Repositories\Room\RoomRepositoryInterface;
use App\Repositories\RoomPricing\EloquentRoomPricingRepository;
use App\Repositories\RoomPricing\RoomPricingRepositoryInterface;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}