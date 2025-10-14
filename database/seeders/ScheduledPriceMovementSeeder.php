<?php

namespace Database\Seeders;

use App\Models\ScheduledPrice;
use App\Models\ScheduledPriceMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduledPriceMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scheduledPrice = ScheduledPrice::first();
        $user = User::first();

        if ($scheduledPrice && $user) {
            ScheduledPriceMovement::create([
                'scheduled_price_id' => $scheduledPrice->id,
                'user_id' => $user->id,
                'action' => 'created',
                'new_price' => $scheduledPrice->price,
                'action_date' => now(),
            ]);
        }
    }
}
