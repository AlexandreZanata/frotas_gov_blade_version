<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\ScheduledPrice;
use App\Models\fuel\ScheduledPriceMovement;
use App\Models\user\User;
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
