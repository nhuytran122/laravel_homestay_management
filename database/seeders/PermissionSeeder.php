<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleSystem;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionEnum::cases() as $perm) {
            Permission::firstOrCreate(['name' => $perm->value]);
        }

        foreach (RoleSystem::cases() as $roleEnum) {
            $role = Role::firstOrCreate(['name' => $roleEnum->value]);

            $permissions = match ($roleEnum) {
                RoleSystem::MANAGER => collect(PermissionEnum::cases())->pluck('value')->toArray(),

                RoleSystem::STAFF => [
                    PermissionEnum::VIEW_ROOM->value,
                    PermissionEnum::VIEW_BOOKING->value,
                    PermissionEnum::CANCEL_BOOKING->value,
                    PermissionEnum::VIEW_CUSTOMER->value,
                    PermissionEnum::EDIT_CUSTOMER->value,
                    PermissionEnum::VIEW_BRANCH->value,
                    PermissionEnum::VIEW_SERVICE->value,
                    PermissionEnum::VIEW_ROOM_TYPE->value,
                    PermissionEnum::VIEW_ROOM_PRICING->value,
                    PermissionEnum::VIEW_CUSTOMER_TYPE->value,
                    PermissionEnum::VIEW_REVIEW->value,
                    PermissionEnum::DELETE_REVIEW->value,
                    PermissionEnum::VIEW_MAINTENANCES->value,
                    PermissionEnum::EDIT_MAINTENANCES->value,
                ],

                RoleSystem::HOUSEKEEPER => [
                    PermissionEnum::VIEW_ROOM->value,
                    PermissionEnum::VIEW_BRANCH->value,
                    PermissionEnum::VIEW_ROOM_TYPE->value,
                    PermissionEnum::VIEW_ROOM_STATUS_HISTORIES->value,
                    PermissionEnum::VIEW_MAINTENANCES->value,
                    PermissionEnum::EDIT_MAINTENANCES->value,
                ],

                RoleSystem::CUSTOMER => [
                    PermissionEnum::VIEW_ROOM->value,
                    PermissionEnum::VIEW_BRANCH->value,
                    PermissionEnum::VIEW_SERVICE->value,
                    PermissionEnum::VIEW_ROOM_TYPE->value,
                    PermissionEnum::VIEW_ROOM_PRICING->value,
                    PermissionEnum::CREATE_BOOKING->value,
                    PermissionEnum::CANCEL_BOOKING->value,
                    PermissionEnum::VIEW_BOOKING->value,
                    PermissionEnum::VIEW_REVIEW->value,
                ],
            };

            $role->syncPermissions($permissions);
        }
    }
}