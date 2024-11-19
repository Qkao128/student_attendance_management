<?php

namespace Database\Seeders;

use App\Enums\UserType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByRole = [
            UserType::SuperAdmin()->key => [],
            UserType::Admin()->key => [],
            UserType::Monitor()->key => []
        ];

        $insertPermissions = fn($role) => collect($permissionsByRole[$role])
            ->map(fn($name) => DB::table('permissions')->insertGetId([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]))
            ->toArray();

        $permissionIdsByRole = [
            UserType::SuperAdmin()->key => $insertPermissions(UserType::SuperAdmin()->key),
            UserType::Admin()->key => $insertPermissions(UserType::Admin()->key),
            UserType::Monitor()->key => $insertPermissions(UserType::Monitor()->key)
        ];

        foreach ($permissionIdsByRole as $role => $permissionIds) {
            $role = Role::create(['name' => $role]);

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIds)->map(fn($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        }
    }
}
