<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatParticipant;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    // Verifica se o usuário autenticado é um participante da sala de chat
    return ChatParticipant::where('user_id', $user->id)
        ->where('chat_room_id', $roomId)
        ->exists();
});
