<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\chat\ChatRoom;
use App\Models\user\Secretariat;
use App\Models\user\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BroadcastMessageController extends Controller
{
    /**
     * Exibir painel de mensagens em massa
     */
    public function index()
    {
        $user = Auth::user();

        // Verificar permissão
        if (!$user->isGeneralManager() && !$user->isSectorManager()) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        // Buscar usuários disponíveis baseado na role
        if ($user->isGeneralManager()) {
            $users = User::with(['role', 'secretariat'])
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();

            $secretariats = Secretariat::orderBy('name')->get();
        } else {
            // Sector manager só vê usuários da sua secretaria
            $users = User::with(['role', 'secretariat'])
                ->where('id', '!=', $user->id)
                ->where('secretariat_id', $user->secretariat_id)
                ->orderBy('name')
                ->get();

            $secretariats = Secretariat::where('id', $user->secretariat_id)->get();
        }

        return view('broadcast-messages.index', compact('users', 'secretariats'));
    }

    /**
     * Enviar mensagens individuais para múltiplos usuários
     */
    public function sendIndividual(Request $request)
    {
        $user = Auth::user();

        // Verificar permissão
        if (!$user->isGeneralManager() && !$user->isSectorManager()) {
            abort(403, 'Você não tem permissão para realizar esta ação.');
        }

        $request->validate([
            'message' => 'required|string|max:5000',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'exists:users,id',
        ]);

        // Verificar se sector manager está tentando enviar para usuários fora da sua secretaria
        if ($user->isSectorManager()) {
            $recipientUsers = User::whereIn('id', $request->recipients)->get();
            foreach ($recipientUsers as $recipient) {
                if ($recipient->secretariat_id !== $user->secretariat_id) {
                    abort(403, 'Você só pode enviar mensagens para usuários da sua secretaria.');
                }
            }
        }

        $sentCount = 0;
        $errors = [];

        foreach ($request->recipients as $recipientId) {
            try {
                // Criar ou buscar sala privada
                $recipient = User::findOrFail($recipientId);

                $chatRoom = ChatRoom::where('type', 'private')
                    ->whereHas('participants', function ($query) use ($recipient) {
                        $query->where('user_id', $recipient->id);
                    })
                    ->whereHas('participants', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->first();

                if (!$chatRoom) {
                    $chatRoom = ChatRoom::create(['type' => 'private']);
                    $chatRoom->participants()->sync([
                        $user->id => ['created_at' => now(), 'updated_at' => now()],
                        $recipient->id => ['created_at' => now(), 'updated_at' => now()]
                    ]);
                }

                // Criar mensagem
                $message = $chatRoom->messages()->create([
                    'user_id' => $user->id,
                    'message' => $request->message,
                ]);

                $message->load(['user', 'readReceipts']);
                $chatRoom->touch();

                // Broadcast
                broadcast(new ChatMessageSent($message))->toOthers();

                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = "Erro ao enviar para {$recipient->name}: " . $e->getMessage();
            }
        }

        if ($sentCount > 0) {
            return redirect()->route('broadcast-messages.index')
                ->with('success', "Mensagens enviadas com sucesso para {$sentCount} usuário(s)!");
        } else {
            return redirect()->route('broadcast-messages.index')
                ->with('error', 'Erro ao enviar mensagens: ' . implode(', ', $errors));
        }
    }

    /**
     * Criar grupo com usuários selecionados
     */
    public function createGroup(Request $request)
    {
        $user = Auth::user();

        // Verificar permissão
        if (!$user->isGeneralManager() && !$user->isSectorManager()) {
            abort(403, 'Você não tem permissão para realizar esta ação.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'nullable|string|max:5000',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
        ]);

        // Verificar se sector manager está tentando criar grupo com usuários fora da sua secretaria
        if ($user->isSectorManager()) {
            $participantUsers = User::whereIn('id', $request->participants)->get();
            foreach ($participantUsers as $participant) {
                if ($participant->secretariat_id !== $user->secretariat_id) {
                    abort(403, 'Você só pode criar grupos com usuários da sua secretaria.');
                }
            }
        }

        try {
            DB::beginTransaction();

            // Criar sala de grupo
            $chatRoom = ChatRoom::create([
                'name' => $request->name,
                'type' => 'group',
            ]);

            // Adicionar participantes (criador + selecionados)
            $participants = array_unique(array_merge([$user->id], $request->participants));
            $chatRoom->participants()->attach($participants);

            // Enviar mensagem inicial se fornecida
            if ($request->message) {
                $message = $chatRoom->messages()->create([
                    'user_id' => $user->id,
                    'message' => $request->message,
                ]);

                $message->load(['user', 'readReceipts']);
                broadcast(new ChatMessageSent($message))->toOthers();
            }

            DB::commit();

            return redirect()->route('chat.index', ['room' => $chatRoom->id])
                ->with('success', 'Grupo criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('broadcast-messages.index')
                ->with('error', 'Erro ao criar grupo: ' . $e->getMessage());
        }
    }

    /**
     * Buscar usuários por secretaria
     */
    public function getUsersBySecretariat(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'secretariat_ids' => 'required|array',
            'secretariat_ids.*' => 'exists:secretariats,id',
        ]);

        $query = User::with(['role', 'secretariat'])
            ->where('id', '!=', $user->id)
            ->whereIn('secretariat_id', $request->secretariat_ids);

        // Se for sector manager, restringir à sua secretaria
        if ($user->isSectorManager()) {
            $query->where('secretariat_id', $user->secretariat_id);
        }

        $users = $query->orderBy('name')->get();

        return response()->json($users->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role->description ?? 'N/A',
                'secretariat' => $u->secretariat->name ?? 'N/A',
            ];
        }));
    }
}

