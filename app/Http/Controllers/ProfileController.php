<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Secretariat;
use App\Models\UserPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $secretariats = Secretariat::orderBy('name')->get();

        return view('profile.edit', [
            'user' => $request->user(),
            'secretariats' => $secretariats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Processar upload da foto
        if ($request->hasFile('photo')) {
            $this->handlePhotoUpload($request->file('photo'), $user);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Handle photo upload - CORRIGIDO para atualizar foto existente
     */
    private function handlePhotoUpload($photo, $user): void
    {
        try {
            // Fazer upload da nova foto
            $path = $photo->store('user-photos', 'public');

            // Buscar foto existente do tipo 'profile' para este usuário
            $existingPhoto = UserPhoto::where('user_id', $user->id)
                ->where('photo_type', 'profile')
                ->first();

            if ($existingPhoto) {
                // Se existe foto anterior, deletar o arquivo físico
                if (Storage::disk('public')->exists($existingPhoto->path)) {
                    Storage::disk('public')->delete($existingPhoto->path);
                }

                // Atualizar o registro existente
                $existingPhoto->update([
                    'path' => $path,
                    'updated_at' => now(),
                ]);

                // Atualizar a referência no usuário
                $user->photo_id = $existingPhoto->id;
            } else {
                // Se não existe foto, criar novo registro
                $userPhoto = UserPhoto::create([
                    'user_id' => $user->id,
                    'photo_type' => 'profile',
                    'path' => $path,
                ]);

                // Associar ao usuário
                $user->photo_id = $userPhoto->id;
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload da foto: ' . $e->getMessage());
            // Não lançar exceção para não quebrar o fluxo
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
