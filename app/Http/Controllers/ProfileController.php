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
    public function edit(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Permite tanto número puro quanto link completo
        $request->validate([
            'link_principal' => ['nullable', 'string', 'max:255'],
            'link_extra1' => ['nullable', 'string', 'max:255'],
            'link_extra2' => ['nullable', 'string', 'max:255'],
        ]);

        $validated = $request->validated();
        $user->fill($validated);

        if ($request->filled('link_principal')) {
            $link = trim($request->link_principal);

            // Se for apenas números (WhatsApp), converte automaticamente
            if (preg_match('/^\d+$/', $link)) {
                $user->link_principal = 'https://wa.me/55' . $link;
            } else {
                $user->link_principal = $link;
            }
        }

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
        if ($user->isDirty('link_principal')) {
            $camposAlterados[] = 'link principal';
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

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        AuditLogger::log('updated_password', get_class($user), $user->id, 'Atualizou a senha');

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    public function updatePhoto(Request $request): JsonResponse
    {
        $request->validate(['photo' => 'required|image|max:2048']);
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('s3')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos', 's3');
        $user->profile_photo_path = $path;
        $user->save();
        $user->refresh();

        AuditLogger::log('updated_photo', get_class($user), $user->id, 'Atualizou a foto de perfil');

        return response()->json([
            'success' => true,
            'message' => 'Foto atualizada com sucesso!',
            'url' => $user->profile_photo_url,
        ], 200);
    }

    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path && Storage::disk('s3')->exists($user->profile_photo_path)) {
            Storage::disk('s3')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();

            AuditLogger::log('deleted_photo', get_class($user), $user->id, 'Removeu a foto de perfil');
        }

        return Redirect::route('profile.edit')->with('status', 'photo-deleted');
    }

    public function updateSlug(Request $request): RedirectResponse
    {
        $request->validate([
            'slug' => ['required', 'string', 'max:255', 'unique:users,slug,' . $request->user()->id],
        ]);

        $user = $request->user();
        $user->slug = $request->slug;
        $user->save();

        AuditLogger::log('updated_slug', get_class($user), $user->id, 'Atualizou o slug');

        return redirect()->route('profile.edit')->with('status', 'slug-updated');
    }

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
