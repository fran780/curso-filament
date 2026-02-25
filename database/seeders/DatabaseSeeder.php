<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // esto de aca sirve para que la primera vez que se cree lo guarde como administrador
        $adminRole = Role::firstOrCreate([ 
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        
        /*// busca un registro en la tabla users que coincida con ciertos atributos (primer argumento). 
        Si lo encuentra, lo actualiza con los nuevos valores (segundo argumento); 
        si no, crea un nuevo registro. Guarda automáticamente el modelo en la base de datos.*/
        $adminUser = User::updateOrCreate(  
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        //Con esto puedo comprobar si al adminUser le falta el $adminRole atributo con el hasRole(), si es falso, lo asigna mediante assignRole()
        if (! $adminUser->hasRole($adminRole->name)) {
            $adminUser->assignRole($adminRole);
        }
    }
}