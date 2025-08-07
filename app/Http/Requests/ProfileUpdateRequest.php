<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'genero' => ['nullable', 'string', 'in:masculino,feminino,outro,prefiro não dizer'],
        ];
    }

    /**
     * Mensagens personalizadas de erro.
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'O campo nome é obrigatório.',
            'name.string'     => 'O nome deve ser uma sequência de caracteres.',
            'name.max'        => 'O nome não pode ultrapassar 255 caracteres.',

            'email.required'  => 'O campo e-mail é obrigatório.',
            'email.string'    => 'O e-mail deve ser uma sequência de caracteres.',
            'email.lowercase' => 'O e-mail deve estar em letras minúsculas.',
            'email.email'     => 'Informe um e-mail válido.',
            'email.max'       => 'O e-mail não pode ultrapassar 255 caracteres.',
            'email.unique'    => 'Este e-mail já está sendo usado por outro usuário.',

            'genero.in'       => 'O campo gênero deve ser Masculino, Feminino, Outro ou Prefiro não dizer.',
        ];
    }
}
