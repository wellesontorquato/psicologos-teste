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
     * Atualização dos dados gerais de perfil.
     *
     * Agora também salva:
     * - CPF
     * - Data de nascimento
     * - Tipo profissional
     * - Registro profissional: CRP, CRM ou outro
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        /*
         * Não usamos apenas $user->fill($validated) aqui para evitar depender
         * totalmente do $fillable do Model User. Assim garantimos que os campos
         * profissionais sejam salvos corretamente.
         */
        $dadosPerfil = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        if (array_key_exists('genero', $validated)) {
            $dadosPerfil['genero'] = $validated['genero'];
        }

        if (array_key_exists('cpf', $validated)) {
            $dadosPerfil['cpf'] = $validated['cpf']
                ? preg_replace('/\D/', '', $validated['cpf'])
                : null;
        }

        if (array_key_exists('data_nascimento', $validated)) {
            $dadosPerfil['data_nascimento'] = $validated['data_nascimento'] ?: null;
        }

        if (array_key_exists('tipo_profissional', $validated)) {
            $dadosPerfil['tipo_profissional'] = $validated['tipo_profissional'] ?: null;
        }

        if (array_key_exists('registro_profissional', $validated)) {
            $dadosPerfil['registro_profissional'] = $validated['registro_profissional']
                ? mb_strtoupper(trim($validated['registro_profissional']), 'UTF-8')
                : null;
        }

        foreach ($dadosPerfil as $campo => $valor) {
            $user->{$campo} = $valor;
        }

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

        if ($user->isDirty('cpf')) {
            $camposAlterados[] = 'CPF';
        }

        if ($user->isDirty('data_nascimento')) {
            $camposAlterados[] = 'data de nascimento';
        }

        if ($user->isDirty('tipo_profissional')) {
            $camposAlterados[] = 'tipo profissional';
        }

        if ($user->isDirty('registro_profissional')) {
            $camposAlterados[] = 'registro profissional';
        }

        $user->save();

        if (!empty($camposAlterados)) {
            AuditLogger::log(
                'updated_profile',
                get_class($user),
                $user->id,
                'Atualizou: ' . implode(', ', $camposAlterados)
            );
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
            'slug'           => ['required', 'string', 'max:255', 'unique:users,slug,' . $user->id],
            'bio'            => ['nullable', 'string', 'max:2000'],
            'whatsapp'       => ['nullable', 'string', 'max:20'],
            'link_principal' => ['nullable', 'string', 'max:255'],
            'link_extra1'    => ['nullable', 'string', 'max:255'],
            'link_extra2'    => ['nullable', 'string', 'max:255'],
            'areas'          => ['nullable', 'array'],
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

    /**
     * Atualiza a foto de perfil no S3 (Contabo) com visibilidade pública e headers corretos.
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $request->validate(['photo' => 'required|image|max:2048']);
        $user = $request->user();

        // Remove a foto antiga (ignora falhas para ser idempotente)
        if ($user->profile_photo_path) {
            try {
                Storage::disk('s3')->delete($user->profile_photo_path);
            } catch (\Throwable $e) {
                // opcional: logar $e->getMessage()
            }
        }

        $uploaded = $request->file('photo');

        // Faz upload público, preservando Content-Type e com cache agressivo
        $path = $uploaded->storePubliclyAs(
            'profile-photos',
            $uploaded->hashName(),
            [
                'disk' => 's3',
                'visibility' => 'public',
                'headers' => [
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                    'Content-Type'  => $uploaded->getMimeType(),
                ],
            ]
        );

        $user->profile_photo_path = $path;
        $user->save();
        $user->refresh();

        AuditLogger::log('updated_photo', get_class($user), $user->id, 'Atualizou a foto de perfil');

        return response()->json([
            'success' => true,
            'message' => 'Foto atualizada com sucesso!',
            'url' => $user->profile_photo_url, // accessor monta CDN + prefixo
        ], 200);
    }

    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            try {
                if (Storage::disk('s3')->exists($user->profile_photo_path)) {
                    Storage::disk('s3')->delete($user->profile_photo_path);
                }
            } catch (\Throwable $e) {
                // opcional: logar $e->getMessage()
            }

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