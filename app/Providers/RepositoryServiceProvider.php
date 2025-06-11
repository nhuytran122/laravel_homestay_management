<?php

namespace App\Providers;

use App\Repositories\CustomerType\CustomerTypeRepositoryInterface;
use App\Repositories\CustomerType\EloquentCustomerTypeRepository;
use App\Repositories\Role\EloquentRoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}