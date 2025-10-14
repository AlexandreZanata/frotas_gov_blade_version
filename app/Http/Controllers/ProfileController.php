<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Secretariat;
use App\Models\UserPhoto;
use App\Models\UserPhotoCnh;
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
        $cnhCategories = \App\Models\CnhCategory::where('is_active', true)->get();

        return view('profile.edit', [
            'user' => $request->user(),
            'secretariats' => $secretariats,
            'cnhCategories' => $cnhCategories,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Validar e atualizar dados básicos
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Processar upload da foto do perfil
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $this->handleProfilePhotoUpload($request->file('photo'), $user);
        }

        // Processar upload da foto da CNH (ADICIONE ESTA VERIFICAÇÃO)
        if ($request->hasFile('cnh_photo') && $request->file('cnh_photo')->isValid()) {
            $this->handleCnhPhotoUpload($request->file('cnh_photo'), $user);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Handle profile photo upload
     */
    private function handleProfilePhotoUpload($photo, $user): bool
    {
        try {
            return \DB::transaction(function () use ($photo, $user) {

                $path = $photo->store('user-photos', 'public');

                $existingPhoto = UserPhoto::where('user_id', $user->id)
                    ->where('photo_type', 'profile')
                    ->first();

                if ($existingPhoto) {
                    if (Storage::disk('public')->exists($existingPhoto->path)) {
                        Storage::disk('public')->delete($existingPhoto->path);
                    }

                    $existingPhoto->update([
                        'path' => $path,
                        'updated_at' => now(),
                    ]);

                    $user->photo_id = $existingPhoto->id;
                } else {
                    $userPhoto = UserPhoto::create([
                        'user_id' => $user->id,
                        'photo_type' => 'profile',
                        'path' => $path,
                    ]);

                    $user->photo_id = $userPhoto->id;
                }

                return true;
            });

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload da foto do perfil: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle CNH photo upload
     */
    private function handleCnhPhotoUpload($photo, $user): bool
    {
        try {
            return \DB::transaction(function () use ($photo, $user) {

                $path = $photo->store('user-cnh-photos', 'public');

                $existingCnhPhoto = UserPhotoCnh::where('user_id', $user->id)
                    ->where('photo_type', 'cnh')
                    ->first();

                if ($existingCnhPhoto) {
                    if (Storage::disk('public')->exists($existingCnhPhoto->path)) {
                        Storage::disk('public')->delete($existingCnhPhoto->path);
                    }

                    $existingCnhPhoto->update([
                        'path' => $path,
                        'updated_at' => now(),
                    ]);

                    $user->photo_cnh_id = $existingCnhPhoto->id;
                } else {
                    $cnhPhoto = UserPhotoCnh::create([
                        'user_id' => $user->id,
                        'photo_type' => 'cnh',
                        'path' => $path,
                    ]);

                    $user->photo_cnh_id = $cnhPhoto->id;
                }

                return true;
            });

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload da foto da CNH: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove profile photo
     */
    public function removeProfilePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->photo) {
            try {
                if (Storage::disk('public')->exists($user->photo->path)) {
                    Storage::disk('public')->delete($user->photo->path);
                }

                $user->photo->delete();
                $user->photo_id = null;
                $user->save();

                return Redirect::route('profile.edit')->with('status', 'profile-photo-removed');
            } catch (\Exception $e) {
                \Log::error('Erro ao remover foto do perfil: ' . $e->getMessage());
                return Redirect::route('profile.edit')->with('error', 'Erro ao remover foto do perfil.');
            }
        }

        return Redirect::route('profile.edit')->with('error', 'Nenhuma foto do perfil para remover.');
    }

    /**
     * Remove CNH photo
     */
    public function removeCnhPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->photoCnh) {
            try {
                if (Storage::disk('public')->exists($user->photoCnh->path)) {
                    Storage::disk('public')->delete($user->photoCnh->path);
                }

                $user->photoCnh->delete();
                $user->photo_cnh_id = null;
                $user->save();

                return Redirect::route('profile.edit')->with('status', 'cnh-photo-removed');
            } catch (\Exception $e) {
                \Log::error('Erro ao remover foto da CNH: ' . $e->getMessage());
                return Redirect::route('profile.edit')->with('error', 'Erro ao remover foto da CNH.');
            }
        }

        return Redirect::route('profile.edit')->with('error', 'Nenhuma foto da CNH para remover.');
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
