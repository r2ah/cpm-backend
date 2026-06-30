<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |-------------------------------------------------
        | ROLES (IDEMPOTENTES)
        |-------------------------------------------------
        */

        $role_admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api'
        ]);

        $role_editor = Role::firstOrCreate([
            'name' => 'editor',
            'guard_name' => 'api'
        ]);

        /*
        |-------------------------------------------------
        | PERMISOS (IDEMPOTENTES)
        |-------------------------------------------------
        */

        $permission_create_role = Permission::firstOrCreate([
            'name' => 'create roles',
            'guard_name' => 'api'
        ]);

        $permission_read_role = Permission::firstOrCreate([
            'name' => 'read roles',
            'guard_name' => 'api'
        ]);

        $permission_update_role = Permission::firstOrCreate([
            'name' => 'update roles',
            'guard_name' => 'api'
        ]);

        $permission_delete_role = Permission::firstOrCreate([
            'name' => 'delete roles',
            'guard_name' => 'api'
        ]);

        $permission_create_opinion = Permission::firstOrCreate([
            'name' => 'create opinions',
            'guard_name' => 'api'
        ]);

        $permission_read_opinion = Permission::firstOrCreate([
            'name' => 'read opinions',
            'guard_name' => 'api'
        ]);

        $permission_update_opinion = Permission::firstOrCreate([
            'name' => 'update opinions',
            'guard_name' => 'api'
        ]);

        $permission_delete_opinion = Permission::firstOrCreate([
            'name' => 'delete opinions',
            'guard_name' => 'api'
        ]);

        $permission_create_person = Permission::firstOrCreate([
            'name' => 'create persons',
            'guard_name' => 'api'
        ]);

        $permission_read_person = Permission::firstOrCreate([
            'name' => 'read persons',
            'guard_name' => 'api'
        ]);

        $permission_update_person = Permission::firstOrCreate([
            'name' => 'update persons',
            'guard_name' => 'api'
        ]);

        $permission_delete_person = Permission::firstOrCreate([
            'name' => 'delete persons',
            'guard_name' => 'api'
        ]);

        $permission_create_proceeding = Permission::firstOrCreate([
            'name' => 'create proceedings',
            'guard_name' => 'api'
        ]);

        $permission_read_proceeding = Permission::firstOrCreate([
            'name' => 'read proceedings',
            'guard_name' => 'api'
        ]);

        $permission_update_proceeding = Permission::firstOrCreate([
            'name' => 'update proceedings',
            'guard_name' => 'api'
        ]);

        $permission_delete_proceeding = Permission::firstOrCreate([
            'name' => 'delete proceedings',
            'guard_name' => 'api'
        ]);

        $permission_create_intervention = Permission::firstOrCreate([
            'name' => 'create interventions',
            'guard_name' => 'api'
        ]);

        $permission_read_intervention = Permission::firstOrCreate([
            'name' => 'read interventions',
            'guard_name' => 'api'
        ]);

        $permission_update_intervention = Permission::firstOrCreate([
            'name' => 'update interventions',
            'guard_name' => 'api'
        ]);

        $permission_delete_intervention = Permission::firstOrCreate([
            'name' => 'delete interventions',
            'guard_name' => 'api'
        ]);

        $permission_create_authority = Permission::firstOrCreate([
            'name' => 'create authorities',
            'guard_name' => 'api'
        ]);

        $permission_read_authority = Permission::firstOrCreate([
            'name' => 'read authorities',
            'guard_name' => 'api'
        ]);

        $permission_update_authority = Permission::firstOrCreate([
            'name' => 'update authorities',
            'guard_name' => 'api'
        ]);

        $permission_delete_authority = Permission::firstOrCreate([
            'name' => 'delete authorities',
            'guard_name' => 'api'
        ]);

        /*
        |-------------------------------------------------
        | ASIGNACIÓN DE PERMISOS A ROLES
        |-------------------------------------------------
        */

        $permissions_admin = [
            $permission_create_role,
            $permission_read_role,
            $permission_update_role,
            $permission_delete_role,

            $permission_create_opinion,
            $permission_read_opinion,
            $permission_update_opinion,
            $permission_delete_opinion,

            $permission_create_person,
            $permission_read_person,
            $permission_update_person,
            $permission_delete_person,

            $permission_create_proceeding,
            $permission_read_proceeding,
            $permission_update_proceeding,
            $permission_delete_proceeding,

            $permission_create_intervention,
            $permission_read_intervention,
            $permission_update_intervention,
            $permission_delete_intervention,

            $permission_create_authority,
            $permission_read_authority,
            $permission_update_authority,
            $permission_delete_authority
        ];

        $permissions_editor = [
            $permission_create_opinion,
            $permission_read_opinion,
            $permission_update_opinion,
            $permission_delete_opinion,

            $permission_create_person,
            $permission_read_person,
            $permission_update_person,
            $permission_delete_person,

            $permission_create_proceeding,
            $permission_read_proceeding,
            $permission_update_proceeding,
            $permission_delete_proceeding,

            $permission_create_intervention,
            $permission_read_intervention,
            $permission_update_intervention,
            $permission_delete_intervention,

            $permission_create_authority,
            $permission_read_authority,
            $permission_update_authority,
            $permission_delete_authority
        ];

        $role_admin->syncPermissions($permissions_admin);
        $role_editor->syncPermissions($permissions_editor);
    }
}