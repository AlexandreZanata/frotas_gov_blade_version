<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar 3 usuários para criar conversas de teste
        $users = User::limit(3)->get();

        if ($users->count() < 2) {
            $this->command->warn('⚠️  Não há usuários suficientes. Execute o UserSeeder primeiro.');
            return;
        }

        $user1 = $users[0];
        $user2 = $users[1];
        $user3 = $users->count() > 2 ? $users[2] : null;

        // Criar conversa privada entre user1 e user2
        $this->command->info("Criando conversa entre {$user1->name} e {$user2->name}...");

        $chatRoom1 = ChatRoom::create([
            'type' => 'private',
        ]);

        $chatRoom1->participants()->attach([$user1->id, $user2->id]);

        // Criar algumas mensagens de teste
        ChatMessage::create([
            'chat_room_id' => $chatRoom1->id,
            'user_id' => $user1->id,
            'message' => 'Olá! Como você está?',
        ]);

        sleep(1); // Pequeno delay para diferentes timestamps

        ChatMessage::create([
            'chat_room_id' => $chatRoom1->id,
            'user_id' => $user2->id,
            'message' => 'Oi! Estou bem, obrigado! E você?',
        ]);

        sleep(1);

        ChatMessage::create([
            'chat_room_id' => $chatRoom1->id,
            'user_id' => $user1->id,
            'message' => 'Também estou bem! Precisamos discutir sobre o projeto.',
        ]);

        $this->command->info('✓ Conversa privada criada com sucesso!');

        // Se houver um terceiro usuário, criar um grupo
        if ($user3) {
            $this->command->info("Criando grupo de chat...");

            $chatRoom2 = ChatRoom::create([
                'name' => 'Equipe de Frotas',
                'type' => 'group',
            ]);

            $chatRoom2->participants()->attach([$user1->id, $user2->id, $user3->id]);

            ChatMessage::create([
                'chat_room_id' => $chatRoom2->id,
                'user_id' => $user1->id,
                'message' => 'Bem-vindos ao grupo da equipe!',
            ]);

            sleep(1);

            ChatMessage::create([
                'chat_room_id' => $chatRoom2->id,
                'user_id' => $user2->id,
                'message' => 'Obrigado! Vamos trabalhar bem juntos.',
            ]);

            sleep(1);

            ChatMessage::create([
                'chat_room_id' => $chatRoom2->id,
                'user_id' => $user3->id,
                'message' => 'Ótimo! Estou animado para começar.',
            ]);

            $this->command->info('✓ Grupo de chat criado com sucesso!');
        }

        $this->command->info('');
        $this->command->info('✅ Chat seeder executado com sucesso!');
        $this->command->info("   - Conversas criadas: " . ChatRoom::count());
        $this->command->info("   - Mensagens criadas: " . ChatMessage::count());
    }
}

