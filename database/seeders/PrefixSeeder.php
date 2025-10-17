<?php
namespace Database\Seeders;

use App\Models\Prefix;
use Illuminate\Database\Seeder;

class PrefixSeeder extends Seeder
{
    public function run(): void
    {
        Prefix::firstOrCreate(['name' => 'V-001']);
        Prefix::firstOrCreate(['name' => 'V-002']);
        Prefix::firstOrCreate(['name' => 'V-003']); 
    }
}
