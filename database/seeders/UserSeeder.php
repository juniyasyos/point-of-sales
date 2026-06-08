<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $superAdminRole = Role::where('name', 'super-admin')->first();
        $permissions = Permission::all();

        if ($superAdminRole) {
            $admin->syncRoles([$superAdminRole->name]);
        }

        $admin->syncPermissions($permissions);

        $cashier = User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Cashier',
                'email' => 'kasir@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $cashierRole = Role::where('name', 'cashier')->first();

        if ($cashierRole) {
            $cashier->syncRoles([$cashierRole->name]);
            $cashier->syncPermissions([]);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return;
        }

        $transactionsPermission = Permission::where('name', 'transactions-access')->first();
        $cashier->syncPermissions($transactionsPermission ? [$transactionsPermission] : []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
