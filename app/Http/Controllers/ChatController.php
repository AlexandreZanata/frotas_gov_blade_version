<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Events\MessageRead;
use App\Events\UserTyping;
use App\Events\UserOnlineStatus;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Exibir lista de conversas
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Marcar usuário como online
        cache()->put('user-online-' . $user->id, true, now()->addMinutes(5));
        cache()->put('user-last-seen-' . $user->id, now(), now()->addDays(7));

        broadcast(new UserOnlineStatus($user->id, true));

        $chatRooms = $user->chatRooms()
            ->with(['participants', 'latestMessage.user'])
            ->get()
            ->map(function ($room) use ($user) {
                $room->unread_count = $room->getUnreadCountForUser($user->id);
                $room->display_name = $room->getDisplayName($user->id);
                $room->avatar_url = $room->getAvatarUrl($user->id);
                $room->other_user = $room->getOtherUser($user->id);
                return $room;
            })
            ->sortByDesc(function ($room) {
                return $room->latestMessage ? $room->latestMessage->created_at : $room->updated_at;
            });

        // Se há um room_id na query, carregar essa conversa
        $activeRoom = null;
        if ($request->has('room')) {
            $activeRoom = ChatRoom::with(['participants', 'messages.user', 'messages.readReceipts'])
                ->findOrFail($request->room);

            // Verificar acesso
            if (!$activeRoom->participants->contains($user->id)) {
                abort(403, 'Você não tem acesso a esta conversa.');
            }
        }

        return view('chat.index', compact('chatRooms', 'activeRoom'));
    }

    /**
     * Buscar mensagens de uma sala específica
     */
    public function getMessages(ChatRoom $chatRoom)
    {
        $user = Auth::user();

        // Verificar se o usuário faz parte da sala
        if (!$chatRoom->participants->contains($user->id)) {
            abort(403, 'Você não tem acesso a esta conversa.');
        }

        $messages = $chatRoom->messages()
            ->with(['user', 'readReceipts'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                    ],
                    'message' => $message->message,
                    'attachment_url' => $message->attachment_url,
                    'attachment_type' => $message->attachment_type,
                    'is_image' => $message->is_image,
                    'is_file' => $message->is_file,
                    'created_at' => $message->created_at->toISOString(),
                    'formatted_time' => $message->formatted_time,
                    'formatted_date' => $message->formatted_date,
                    'is_today' => $message->is_today,
                    'read_status' => $message->getReadStatus(),
                    'read_receipts' => $message->readReceipts->map(function ($receipt) {
                        return [
                            'user_id' => $receipt->user_id,
                            'read_at' => $receipt->read_at->toISOString(),
                        ];
                    }),
                ];
            });

        return response()->json($messages);
    }

    /**
     * Enviar nova mensagem
     */
    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        $user = Auth::user();

        // Verificar se o usuário faz parte da sala
        if (!$chatRoom->participants->contains($user->id)) {
            abort(403, 'Você não tem acesso a esta conversa.');
        }

        $request->validate([
            'message' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:10240', // 10MB
        ]);

        $attachmentPath = null;
        $attachmentType = null;

        // Upload de anexo se fornecido
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');

            // Detectar tipo
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $attachmentType = 'image';
            } else {
                $attachmentType = 'file';
            }
        }

        $message = $chatRoom->messages()->create([
            'user_id' => $user->id,
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        // Carregar relacionamentos
        $message->load(['user', 'readReceipts']);

        // Atualizar timestamp da sala
        $chatRoom->touch();

        // Broadcast para outros usuários
        broadcast(new ChatMessageSent($message))->toOthers();

        return response()->json([
            'id' => $message->id,
            'user_id' => $message->user_id,
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
            ],
            'message' => $message->message,
            'attachment_url' => $message->attachment_url,
            'attachment_type' => $message->attachment_type,
            'is_image' => $message->is_image,
            'is_file' => $message->is_file,
            'created_at' => $message->created_at->toISOString(),
            'formatted_time' => $message->formatted_time,
            'formatted_date' => $message->formatted_date,
            'read_status' => $message->getReadStatus(),
        ], 201);
    }

    /**
     * Criar ou buscar conversa privada com outro usuário
     */
    public function getOrCreatePrivateChat(User $user)
    {
        $currentUser = Auth::user();

        if ($user->id === $currentUser->id) {
            abort(400, 'Você não pode criar uma conversa consigo mesmo.');
        }

        // Buscar se já existe uma conversa privada entre os dois usuários
        $chatRoom = ChatRoom::where('type', 'private')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('participants', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id);
            })
            ->first();

        if (!$chatRoom) {
            // Criar nova sala privada
            $chatRoom = ChatRoom::create([
                'type' => 'private',
            ]);

            // Adicionar os dois participantes
            $chatRoom->participants()->attach([$currentUser->id, $user->id]);
        }

        return redirect()->route('chat.index', ['room' => $chatRoom->id]);
    }

    /**
     * Criar grupo de chat
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
        ]);

        $chatRoom = ChatRoom::create([
            'name' => $request->name,
            'type' => 'group',
        ]);

        // Adicionar o criador e os participantes
        $participants = array_unique(array_merge([$request->user()->id], $request->participants));
        $chatRoom->participants()->attach($participants);

        return response()->json($chatRoom->load('participants'), 201);
    }

    /**
     * Marcar mensagens como lidas
     */
    public function markAsRead(Request $request, ChatRoom $chatRoom)
    {
        $user = Auth::user();

        if (!$chatRoom->participants->contains($user->id)) {
            abort(403, 'Você não tem acesso a esta conversa.');
        }

        // Atualizar last_read_at
        DB::table('chat_participants')
            ->where('chat_room_id', $chatRoom->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        // Marcar mensagens individuais como lidas
        $messageIds = $request->input('message_ids', []);

        if (!empty($messageIds)) {
            foreach ($messageIds as $messageId) {
                $message = ChatMessage::find($messageId);
                if ($message && $message->chat_room_id === $chatRoom->id) {
                    $message->markAsReadBy($user->id);

                    // Broadcast confirmação de leitura
                    broadcast(new MessageRead($message, $user->id))->toOthers();
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Notificar que usuário está digitando
     */
    public function typing(Request $request, ChatRoom $chatRoom)
    {
        $user = Auth::user();

        if (!$chatRoom->participants->contains($user->id)) {
            abort(403);
        }

        $isTyping = $request->input('typing', true);

        broadcast(new UserTyping($user->id, $user->name, $chatRoom->id, $isTyping))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Buscar usuários para iniciar chat
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('q', '');

        $users = User::where('id', '!=', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3b82f6&color=fff',
                    'is_online' => $user->isOnline(),
                ];
            });

        return response()->json($users);
    }

    /**
     * Upload de arquivo/imagem
     */
    public function uploadAttachment(Request $request, ChatRoom $chatRoom)
    {
        $user = Auth::user();

        if (!$chatRoom->participants->contains($user->id)) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('chat-attachments', 'public');

        $mimeType = $file->getMimeType();
        $type = str_starts_with($mimeType, 'image/') ? 'image' : 'file';

        return response()->json([
            'path' => $path,
            'url' => Storage::url($path),
            'type' => $type,
            'name' => $file->getClientOriginalName(),
        ]);
    }
}
