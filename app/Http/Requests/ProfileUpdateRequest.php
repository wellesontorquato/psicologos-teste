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
     * Prepara os dados antes da validação.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => $this->input('cpf')
                ? preg_replace('/\D/', '', (string) $this->input('cpf'))
                : null,

            'registro_profissional' => $this->input('registro_profissional')
                ? mb_strtoupper(trim((string) $this->input('registro_profissional')))
                : null,
        ]);
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

            'genero' => [
                'nullable',
                'string',
                'in:masculino,feminino,outro,prefiro não dizer',
            ],

            'cpf' => [
                'nullable',
                'string',
                'size:11',
                Rule::unique(User::class, 'cpf')->ignore($this->user()->id),
            ],

            'data_nascimento' => [
                'nullable',
                'date',
            ],

            'tipo_profissional' => [
                'nullable',
                'string',
                Rule::in([
                    'psicologo',
                    'psiquiatra',
                    'psicanalista',
                ]),
            ],

            'registro_profissional' => [
                Rule::requiredIf(fn () => in_array($this->input('tipo_profissional'), [
                    'psicologo',
                    'psiquiatra',
                ])),
                'nullable',
                'string',
                'max:30',
            ],
        ];
    }

    /**
     * Mensagens personalizadas de erro.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string'   => 'O nome deve ser uma sequência de caracteres.',
            'name.max'      => 'O nome não pode ultrapassar 255 caracteres.',

            'email.required'  => 'O campo e-mail é obrigatório.',
            'email.string'    => 'O e-mail deve ser uma sequência de caracteres.',
            'email.lowercase' => 'O e-mail deve estar em letras minúsculas.',
            'email.email'     => 'Informe um e-mail válido.',
            'email.max'       => 'O e-mail não pode ultrapassar 255 caracteres.',
            'email.unique'    => 'Este e-mail já está sendo usado por outro usuário.',

            'genero.in' => 'O campo gênero deve ser Masculino, Feminino, Outro ou Prefiro não dizer.',

            'cpf.string' => 'O CPF deve ser uma sequência de caracteres.',
            'cpf.size'   => 'Informe um CPF válido com 11 números.',
            'cpf.unique' => 'Este CPF já está sendo usado por outro usuário.',

            'data_nascimento.date' => 'Informe uma data de nascimento válida.',

            'tipo_profissional.string' => 'O tipo profissional deve ser uma sequência de caracteres.',
            'tipo_profissional.in'     => 'O tipo profissional deve ser Psicólogo(a), Psiquiatra ou Psicanalista.',

            'registro_profissional.required' => 'O registro profissional é obrigatório para psicólogo(a) e psiquiatra.',
            'registro_profissional.string'   => 'O registro profissional deve ser uma sequência de caracteres.',
            'registro_profissional.max'      => 'O registro profissional não pode ultrapassar 30 caracteres.',
        ];
    }
}