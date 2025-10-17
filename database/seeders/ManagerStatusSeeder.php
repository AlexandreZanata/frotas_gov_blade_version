<?php

namespace Database\Seeders;

use App\Models\Manager\ManagerStatus;
use Illuminate\Database\Seeder;

class ManagerStatusSeeder extends Seeder
{
    public function run(): void
    {
        ManagerStatus::firstOrCreate(['name' => 'active'], ['description' => 'Ativo']);
        ManagerStatus::firstOrCreate(['name' => 'inactive'], ['description' => 'Inativo']);
        ManagerStatus::firstOrCreate(['name' => 'on_leave'], ['description' => 'Afastado']);
    }
}
