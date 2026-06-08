<?php

return [

    'accepted'             => 'O campo :attribute deve ser aceito.',
    'active_url'           => 'O campo :attribute não é uma URL válida.',
    'after'                => 'O campo :attribute deve ser uma data posterior a :date.',
    'alpha'                => 'O campo :attribute deve conter apenas letras.',
    'alpha_num'            => 'O campo :attribute deve conter apenas letras e números.',
    'array'                => 'O campo :attribute deve ser um array.',
    'before'               => 'O campo :attribute deve ser uma data anterior a :date.',
    'between'              => [
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'string'  => 'O campo :attribute deve conter entre :min e :max caracteres.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'A confirmação de :attribute não corresponde.',
    'email'                => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'required'             => 'O campo :attribute é obrigatório.',
    'unique'               => 'Este :attribute já está em uso.',
    'max'                  => [
        'string'  => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],
    'min'                  => [
        'string'  => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],
    'in'                   => 'O campo :attribute selecionado é inválido.',

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'genero' => 'gênero',
        'cpf' => 'CPF',
        'crp' => 'CRP',
        'data_nascimento' => 'data de nascimento',
    ],
];
