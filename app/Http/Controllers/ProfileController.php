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

    /**
     * Atualização dos dados gerais de perfil (nome, email, etc).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill($validated);

        $camposAlterados = [];
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $camposAlterados[] = 'e-mail';
        }
        if ($user->isDirty('name')) {
            $camposAlterados[] = 'nome';
        }
        if ($user->isDirty('genero')) {
            $camposAlterados[] = 'gênero';
        }

        $user->save();

        if (!empty($camposAlterados)) {
            AuditLogger::log('updated_profile', get_class($user), $user->id, 'Atualizou: ' . implode(', ', $camposAlterados));
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Atualização exclusiva da Landing Page Pública.
     */
    public function updateLanding(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'slug'          => ['required', 'string', 'max:255', 'unique:users,slug,' . $user->id],
            'bio'           => ['nullable', 'string', 'max:2000'],
            'whatsapp'      => ['nullable', 'string', 'max:20'],
            'link_principal'=> ['nullable', 'string', 'max:255'],
            'link_extra1'   => ['nullable', 'string', 'max:255'],
            'link_extra2'   => ['nullable', 'string', 'max:255'],
            'areas'         => ['nullable', 'array'],
        ]);

        $user->slug = $request->slug;
        $user->bio = $request->bio;
        $user->whatsapp = $request->whatsapp ? preg_replace('/\D/', '', $request->whatsapp) : null;

        // Converte link principal se for apenas números
        if ($request->filled('link_principal')) {
            $link = trim($request->link_principal);
            $user->link_principal = preg_match('/^\d+$/', $link)
                ? 'https://wa.me/55' . $link
                : $link;
        } else {
            $user->link_principal = null;
        }

        $user->link_extra1 = $request->link_extra1 ?? null;
        $user->link_extra2 = $request->link_extra2 ?? null;
        $user->areas = $request->areas ? json_encode($request->areas) : null;

        $user->save();

        AuditLogger::log('updated_landing', get_class($user), $user->id, 'Atualizou a página pública');

        return Redirect::route('profile.edit', ['tab' => 'pagina-publica'])
            ->with('status', 'landing-updated');
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
