<?php

namespace Database\Seeders;

use App\Models\GarbageUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class GarbageUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'motorista@frotas.gov')->first(); // Pega um motorista para exemplo
        if ($user) {
            GarbageUser::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
