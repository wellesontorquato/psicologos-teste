<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Exibe o formulário de edição do perfil do usuário.
     */
    public function edit(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atualiza as informações do perfil do usuário.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        $camposAlterados = [];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $camposAlterados[] = 'e-mail';
        }

        if ($user->isDirty('genero')) {
            $camposAlterados[] = 'gênero';
        }

        if ($user->isDirty('name')) {
            $camposAlterados[] = 'nome';
        }

        $user->save();

        AuditLogger::log(
            'updated_profile',
            get_class($user),
            $user->id,
            'Atualizou: ' . implode(', ', $camposAlterados)
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Atualiza a senha do usuário.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        AuditLogger::log('updated_password', get_class($user), $user->id, 'Atualizou a senha');

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Atualiza a foto de perfil do usuário.
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $request->validate(['photo' => 'required|image|max:2048']);

        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos', 's3');
        $user->profile_photo_path = $path;
        $user->save();
        $user->refresh();

        AuditLogger::log('updated_photo', get_class($user), $user->id, 'Atualizou a foto de perfil');

        return response()->json([
            'success' => true,
            'message' => 'Foto atualizada com sucesso!',
            'url' => Storage::disk('s3')->url($path),
        ], 200);
    }

    /**
     * Remove a foto de perfil do usuário.
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();

            AuditLogger::log('deleted_photo', get_class($user), $user->id, 'Removeu a foto de perfil');
        }

        return Redirect::route('profile.edit')->with('status', 'photo-deleted');
    }

    /**
     * Exclui a conta do usuário.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        AuditLogger::log('deleted_account', get_class($user), $user->id, 'Conta excluída');

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
