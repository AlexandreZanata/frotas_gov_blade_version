<?php
namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Encontra o Admin e cria um novo usuário "Motorista"
        $adminUser = User::where('email', 'admin@frotas.gov')->firstOrFail();
        $driverUser = User::firstOrCreate(
            ['email' => 'motorista@frotas.gov'],
            [
                'name' => 'Motorista Teste',
                'cpf' => '11122233344',
                'password' => Hash::make('password'),
                'role_id' => \App\Models\Role::where('name', 'driver')->first()->id,
                'secretariat_id' => \App\Models\Secretariat::where('name', 'Obras')->first()->id,
            ]
        );

        // 2. Cria uma sala de chat privada entre eles
        $room = ChatRoom::create(['type' => 'private']);

        // 3. Adiciona os dois usuários como participantes da sala
        ChatParticipant::create(['chat_room_id' => $room->id, 'user_id' => $adminUser->id]);
        ChatParticipant::create(['chat_room_id' => $room->id, 'user_id' => $driverUser->id]);

        // 4. Simula uma pequena conversa
        $msg1 = ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $adminUser->id,
            'message' => 'Olá, Motorista. Por favor, lembre-se de calibrar os pneus do veículo BRA2E19.'
        ]);

        sleep(1); // Pequena pausa para diferenciar os timestamps

        $msg2 = ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $driverUser->id,
            'message' => 'Entendido, Gestor. Farei isso agora mesmo.'
        ]);
    }
}
