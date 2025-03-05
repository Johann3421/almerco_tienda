<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un rol de administrador
        $adminRole = Role::create(['name' => 'admin']);

        // Crear permisos
        $permissions = [
            'create_users',
            'edit_users',
            'delete_users',
            'dashboard',
            'products',
            'categories',
            'orders',
            'banners',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar permisos al rol de administrador
        $adminRole->syncPermissions($permissions);

        // Crear un usuario
        $user = User::create([
            'name' => 'Diego Carlos',
            'email' => 'testeosac616@gmail.com',
            'password' => Hash::make('12345678'),
            'token' => Str::uuid(),
            'active' => true,
        ]);

        // Asignar el rol de administrador al usuario
        $user->assignRole($adminRole);

        // Crear un usuario
        $user1 = User::create([
            'name' => 'Johann Abad',
            'email' => '123@gmail.com',
            'password' => Hash::make('12345678'),
            'token' => Str::uuid(),
            'active' => true,
        ]);

        // Asignar el rol de administrador al usuario
        $user1->assignRole($adminRole);
    }
}
