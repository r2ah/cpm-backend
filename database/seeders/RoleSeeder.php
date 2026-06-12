<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name' => 'admin']);
        $role_editor = Role::create(['name' => 'editor']);

		$permission_create_role = Permission::create(['name' => 'create roles']);
		$permission_read_role = Permission::create(['name' => 'read roles']);
		$permission_update_role = Permission::create(['name' => 'update roles']);
		$permission_delete_role = Permission::create(['name' => 'delete roles']);

		$permission_create_opinion = Permission::create(['name' => 'create opinions']);
		$permission_read_opinion = Permission::create(['name' => 'read opinions']);
		$permission_update_opinion = Permission::create(['name' => 'update opinions']);
		$permission_delete_opinion = Permission::create(['name' => 'delete opinions']);

		$permission_create_person = Permission::create(['name' => 'create persons']);
		$permission_read_person = Permission::create(['name' => 'read persons']);
		$permission_update_person = Permission::create(['name' => 'update persons']);
		$permission_delete_person = Permission::create(['name' => 'delete persons']);

		$permission_create_proceeding = Permission::create(['name' => 'create proceedings']);
		$permission_read_proceeding = Permission::create(['name' => 'read proceedings']);
		$permission_update_proceeding = Permission::create(['name' => 'update proceedings']);
		$permission_delete_proceeding = Permission::create(['name' => 'delete proceedings']);

		$permission_create_intervention = Permission::create(['name' => 'create interventions']);
		$permission_read_intervention = Permission::create(['name' => 'read interventions']);
		$permission_update_intervention = Permission::create(['name' => 'update interventions']);
		$permission_delete_intervention = Permission::create(['name' => 'delete interventions']);

		$permission_create_authority = Permission::create(['name' => 'create authorities']);
		$permission_read_authority = Permission::create(['name' => 'read authorities']);
		$permission_update_authority = Permission::create(['name' => 'update authorities']);
		$permission_delete_authority = Permission::create(['name' => 'delete authorities']);

		$permissions_admin = [
			$permission_create_role, $permission_read_role, $permission_update_role, $permission_delete_role,
			$permission_create_opinion, $permission_read_opinion, $permission_update_opinion, $permission_delete_opinion,
			$permission_create_person, $permission_read_person, $permission_update_person, $permission_delete_person,
			$permission_create_proceeding, $permission_read_proceeding, $permission_update_proceeding, $permission_delete_proceeding,
			$permission_create_intervention, $permission_read_intervention, $permission_update_intervention, $permission_delete_intervention,
			$permission_create_authority, $permission_read_authority, $permission_update_authority, $permission_delete_authority
		];

		$permissions_editor = [
			$permission_create_opinion, $permission_read_opinion, $permission_update_opinion, $permission_delete_opinion,
			$permission_create_person, $permission_read_person, $permission_update_person, $permission_delete_person,
			$permission_create_proceeding, $permission_read_proceeding, $permission_update_proceeding, $permission_delete_proceeding,
			$permission_create_intervention, $permission_read_intervention, $permission_update_intervention, $permission_delete_intervention,
			$permission_create_authority, $permission_read_authority, $permission_update_authority, $permission_delete_authority
		];

		$role_admin->syncPermissions($permissions_admin);
		$role_editor->syncPermissions($permissions_editor);
    }
}
