<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador',   'slug' => 'ADMIN_BARRIO'],
            ['name' => 'Gestor de pago',  'slug' => 'GESTOR_PAGO'],
            ['name' => 'Guardia',         'slug' => 'GUARDIA'],
            ['name' => 'Beneficiario',    'slug' => 'BENEFICIARIO'],
        ];

        foreach ($roles as $r) {
            Role::query()->firstOrCreate(['slug' => $r['slug']], $r);
        }
    }
}
