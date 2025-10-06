<?php
namespace Database\Seeders;
use App\Models\Prefix;
use Illuminate\Database\Seeder;

class PrefixSeeder extends Seeder {
    public function run(): void {
        Prefix::create(['name' => 'V-001']);
        Prefix::create(['name' => 'S-102']);
        Prefix::create(['name' => 'E-030']);
    }
}
